<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Helpers\UtilityHelper;
use App\Http\Resources\ChatSoundResource;
use App\Http\Resources\UserApplicationSettingsResource;
use App\Models\ApplicationChatSound;
use App\Models\ApplicationSettings;
use App\Models\UserApplicationSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserApplicationSettingsController extends Controller
{

    public function getUserApplicationSettings()
    {
        $userApplicationSettings = UserApplicationSettings::where('user_id', auth()->user()->id)->first();
        $applicationSettings = ApplicationSettings::first();
        $applicationSound = ApplicationChatSound::all();
        return response()->json(
            [
                SuccessStatus::DATA => new UserApplicationSettingsResource($userApplicationSettings),
                'sounds' =>  ChatSoundResource::collection($applicationSound),
                'about' => [
                    'bundle_info' => $applicationSettings->bundle_info,
                    'about_us_link' => $applicationSettings->about_us_link,
                    'operating_system' => UtilityHelper::getUserOS()
                ]
            ]
        );
    }

    public function updateUserApplicationSettings(Request $request)
    {

        $rules = array(
            'theme_color' => 'required|string|max:255',
            'enable_browser_notification' => 'required|boolean',
            'language' => 'required|string',
            'new_chat_sound' => 'required|array',
            'existing_chat_sound' => 'required|array',
            'new_website_visitor_sound' => 'required|array',
            'enable_sound_for_new_visitor' => 'required|boolean',
        );

        $messages = [
            'theme_color.required' => '* Theme color field is required',
            'enable_browser_notification.string' => '* Enable browser notification field is required',
            'language.max' => '* Language field is required',
            'new_chat_sound.max' => '* New chat sound field is required',
            'existing_chat_sound.max' => '* Existing chat sound field is required',
            'new_website_visitor_sound.max' => '* New Website visitor sound field is required',
            'enable_sound_for_new_visitor.max' => '* Enable sound for new visitor field is required',

        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $userApplicationSettings = UserApplicationSettings::where('user_id', auth()->user()->id)->first();
            $userApplicationSettings->theme_color = $request->theme_color;
            $userApplicationSettings->enable_browser_notification = $request->enable_browser_notification;
            $userApplicationSettings->language = $request->language;
            $userApplicationSettings->new_chat_sound = $request->new_chat_sound['id'];
            $userApplicationSettings->existing_chat_sound = $request->existing_chat_sound['id'];
            $userApplicationSettings->new_website_visitor_sound = $request->new_website_visitor_sound['id'];
            $userApplicationSettings->enable_sound_for_new_visitor = $request->enable_sound_for_new_visitor;
            $userApplicationSettings->save();

            return response()->json([SuccessStatus::DATA => "Application settings updated successfully"]);

        }
    }
}
