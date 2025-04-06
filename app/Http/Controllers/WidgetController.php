<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Helpers\UtilityHelper;
use App\Helpers\WidgetFields;
use App\Http\Resources\WidgetResource;
use App\Http\Resources\WidgetSettingsResource;
use App\Models\OrganizationSettings;
use App\Models\UserOrganization;
use App\Models\Widget;
use App\Models\WidgetAddon;
use App\Models\WidgetAdvanceCustomization;
use App\Models\WidgetContactInformation;
use App\Models\WidgetSettings;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class WidgetController extends Controller
{
    public function getWidget(): JsonResponse
    {
        try {

            // Todo check access permissions, if user is admin and can update widget
            // We don't want an invited user to initiate the widget creation flow
            // even tho it doesn't matter.

            $organizationId = UserOrganization::where("user_id", auth()->user()->id)->firstOrFail();
            $widget = Widget::where('organization_id', $organizationId->organization_id)->first();
            $widgetSettings = WidgetSettings::first();

            if(!$widget){
                $widget = $this->createWidgetProperty();
            }

            return response()->json([
                'data' => new WidgetResource($widget),
                'settings' => new WidgetSettingsResource($widgetSettings)
            ]);

        } catch (\Exception $exception){
            return response()->json([ErrorStatus::SYSTEM_ERROR => $exception->getMessage()]);
        }
    }

    /**
     * @throws Exception
     */
    private function createWidgetProperty(): Widget|Exception
    {
        try {
            $alias = UtilityHelper::generateRandomHash();
            $widget = $this->saveWidgetOptions($alias);
            $this->createDefaultWidgetContactInformation($widget);
            $this->createDefaultWidgetAdvanceCustomization($widget);
            $this->createDefaultWidgetAddons($widget);
            return $widget;
        } catch (\Exception $ex){
            throw new Exception("System error: sorry unable to process request at this time");
        }
    }


    private function saveWidgetOptions($alias): Widget {
        $userOrganization = UserOrganization::where("user_id", auth()->user()->id)->first();
        $organizationSettings = OrganizationSettings::where('id', $userOrganization->organization_id)->first();
        $widget = new Widget();
        $widget->id_alias = $alias;
        $widget->website_name = $organizationSettings->name;
        $widget->website_domain = $organizationSettings->website;
        $widget->organization_id = $userOrganization->organization_id;
        $widget->save();
        return $widget;
    }
    private function createDefaultWidgetContactInformation($widget): void{
        $widget_contact_information = new WidgetContactInformation();
        $widget_contact_information->widget_id = $widget->id;
        $widget_contact_information->save();
    }

    private function createDefaultWidgetAdvanceCustomization($widget): void{
        $widget_advance_customization = new WidgetAdvanceCustomization();
        $widget_advance_customization->widget_id = $widget->id;
        $widget_advance_customization->chat_appearance =
            json_encode(
                [
                    "default_theme_options" => "blue",
                    "theme_text_options" => "theme_1",
                    "welcome_message_options" => "theme_3",
                    "background_image_options" => "none",
                ]
            );
        $widget_advance_customization->chat_visibility =
            json_encode(
                [
                    "reverse_chatbox_position" => false,
                    "show_on_desktop" => true,
                    "show_on_tablet" => true,
                    "show_on_mobile" => true,
                    "show_chat_representative_profile_image" => true,
                ]
            );
        $widget_advance_customization->chat_behaviour =
            json_encode(
                [
                    "hide_if_away" => false,
                    "ask_for_email" => true,
                    "ask_for_phone_number" => false,
                    "force_identification" => false,
                    "enable_files_upload" => true,
                    "show_read_receipt" => true,
                    "live_typing" => false,
                ]
            );
        $widget_advance_customization->chat_restrictions =
            json_encode(
                [
                    "hide_chat_box_from_location" => [],
                    "show_chatbox_for_people_only_in_location" => [],
                    "hide_chatbox_from_pages" => [],
                    "hide_chatbox_from_ip" => [],
                ]
            );
        $widget_advance_customization->chat_security =
            json_encode([ "lock_chatbox_to_domain_and_subdomains_only" => true] );
        $widget_advance_customization->save();
    }

    private function createDefaultWidgetAddons($widget): void{
        $widget_addon = new WidgetAddon();
        $widget_addon->widget_id = $widget->id;
        $widget_addon->enable_emojis = true;
        $widget_addon->prevent_profanity = false;
        $widget_addon->save();
    }

    public function updateWidget(Request $request){
        $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();

        if(!$userOrganization){
            return response()->json([ErrorStatus::REQUEST_ERROR => "Sorry unable to perform operation at this time"]);
        }
        $widget = Widget::where('organization_id', $userOrganization->organization_id)->firstOrFail();
        //Todo: check if user has widget edit rights or permissions
        try {
            switch($request->field) {
                case WidgetFields::CONTACT_INFORMATION :
                    $data = $this->validateContactInfoFieldSet($request->data[WidgetFields::CONTACT_INFORMATION]);
                    return $this->updateContactFieldSet($widget, $data);
                case  WidgetFields::WIDGET_ADDON :
                    $data = $this->validateAddonFieldSet($request->data[WidgetFields::WIDGET_ADDON]);
                    return $this->updateAddonFieldSet($widget, $data);
                case  WidgetFields::WIDGET_CUSTOMIZATION :
                    // Todo: Validate FieldSet
                    // $data = $this->validateAddonFieldSet($request->data[WidgetFields::WIDGET_CUSTOMIZATION]);
                    if($request->field_category == 'appearance'){
                        return $this->updateCustomizationApperanceFieldSet($widget, $request->data[WidgetFields::WIDGET_CUSTOMIZATION]);
                    } elseif ($request->field_category == "behaviour"){
                        return $this->updateCustomizationBehaviourFieldSet($widget, $request->data[WidgetFields::WIDGET_CUSTOMIZATION]);
                    } elseif ($request->field_category == "visibility"){
                        return $this->updateCustomizationVisibilityFieldSet($widget, $request->data[WidgetFields::WIDGET_CUSTOMIZATION]);
                    } elseif ($request->field_category == "security"){
                        return $this->updateCustomizationSecurityFieldSet($widget, $request->data[WidgetFields::WIDGET_CUSTOMIZATION]);
                    } elseif ($request->field_category == "restriction"){
                        return $this->updateCustomizationRestrictionFieldSet($widget, $request->data[WidgetFields::WIDGET_CUSTOMIZATION]);
                    }
                    return response()->json([ErrorStatus::ERROR => [ErrorStatus::ERROR => ["Invalid request: Invalid selection"]]], 422);

                default:
                    return response()->json([ErrorStatus::ERROR => [ErrorStatus::ERROR => ["Invalid request: Invalid selection"]]], 422);
            }
        } catch (InvalidArgumentException $exception){
            return response()->json([ErrorStatus::ERROR => json_decode($exception->getMessage())], 422);
        }

    }
    private function updateCustomizationRestrictionFieldSet($widget, $data){
        $widget->WidgetAdvanceCustomization->chat_restrictions = $data['chat_restrictions'];
        $widget->WidgetAdvanceCustomization->save();
        return response()->json([SuccessStatus::DATA => "Widget restrictions updated successfully"]);
    }

    private function updateCustomizationSecurityFieldSet($widget, $data){
        $widget->WidgetAdvanceCustomization->chat_security = $data['chat_security'];
        $widget->WidgetAdvanceCustomization->save();
        return response()->json([SuccessStatus::DATA => "Widget security updated successfully"]);
    }
    private function updateCustomizationVisibilityFieldSet($widget, $data){
        $widget->WidgetAdvanceCustomization->chat_visibility = $data['chat_visibility'];
        $widget->WidgetAdvanceCustomization->save();
        return response()->json([SuccessStatus::DATA => "Widget visibility updated successfully"]);
    }

    private function updateCustomizationBehaviourFieldSet($widget, $data){
        $widget->WidgetAdvanceCustomization->chat_behaviour = $data['chat_behaviour'];
        $widget->WidgetAdvanceCustomization->save();
        return response()->json([SuccessStatus::DATA => "Widget behaviour updated successfully"]);
    }
    private function updateCustomizationApperanceFieldSet($widget, $data){
        $widget->WidgetAdvanceCustomization->chat_appearance = $data['chat_appearance'];
        $widget->WidgetAdvanceCustomization->save();
        return response()->json([SuccessStatus::DATA => "Widget appearance updated successfully"]);
    }
    private function updateAddonFieldSet($widget, $data){
        $widget->WidgetAddon->enable_emojis = $data['enable_emojis'];
        $widget->WidgetAddon->prevent_profanity = $data['prevent_profanity'];
        $widget->WidgetAddon->save();
        return response()->json([SuccessStatus::DATA => "Widget updated successfully"]);
    }
    private  function validateAddonFieldSet($field)
    {
        $rules = array(
            'enable_emojis' => 'boolean',
            'prevent_profanity' => 'boolean',
        );

        $messages = [
            'enable_emojis.boolean' => '* Enable emojis has invalid characters',
            'prevent_profanity.boolean' => '* Enable profanity has invalid characters',
        ];
        $validator = Validator::make($field, $rules, $messages);
        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        } else {
            return $field;
        }
    }
    /**
     * @throws InvalidArgumentException
     */
    private function validateContactInfoFieldSet($field)
    {

        $rules = array(
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'messenger' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
        );

        $messages = [
            'email.string' => '* Email invalid characters',
            'email.email' => '* Email must be of email format with \'@\' symbol',
            'email.max' => '* Email is too long',

            'phone.string' => 'Phone number contains invalid characters',

            'messenger.string' => 'Messenger contains I=invalid characters',

            'twitter.string' => 'Twitter contains invalid characters',

            'instagram.string' => 'Instagram contains invalid characters',
        ];
        $validator = Validator::make($field, $rules, $messages);
        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        } else {
            return $field;
        }
    }

    private function updateContactFieldSet($widget, $data)
    {
        $widgetContactInfo = WidgetContactInformation::where('widget_id', $widget->id)->firstOrFail();
        $widgetContactInfo->email = $data['email'];
        $widgetContactInfo->phone = $data['phone'];
        $widgetContactInfo->messenger = $data['messenger'];
        $widgetContactInfo->telegram = $data['telegram'];
        $widgetContactInfo->twitter = $data['twitter'];
        $widgetContactInfo->whatsapp = $data['whatsapp'];
        $widgetContactInfo->instagram = $data['instagram'];
        $widgetContactInfo->save();

        return response()->json([SuccessStatus::DATA => "Widget updated successfully"]);
    }
}
