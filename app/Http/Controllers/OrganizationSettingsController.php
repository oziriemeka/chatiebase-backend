<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Http\Resources\CountryResource;
use App\Http\Resources\OrganizationSettingsResource;
use App\Models\Country;
use App\Models\OrganizationSettings;
use App\Models\UserOrganization;
use App\Models\Widget;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationSettingsController extends Controller
{
    public function getOrganizationSettings(){
        //Todo: check if user have permission to do this

        $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();
        $organizationSettings = OrganizationSettings::where("id", $userOrganization->organization_id)->first();
        $countries = Country::get()->toArray();
        return response()->json([
            'data' => new OrganizationSettingsResource($organizationSettings),
            'timezones' => $this->getTimeZone(),
            'countries' => array_map(fn($country) => ["id" => $country['name'], "name" => $country['name']], $countries)
        ]);
    }

    public function getTimeZone()
    {
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        return array_map(fn($tz) => ['id' => $tz, 'name' => $tz], $timezones);

    }

    public function updateOrganizationSettings(Request $request)
    {

        $rules = array(
            'name' => 'required|string|max:255',
            'website' => 'required|url|max:255',
            'country' => 'required|string|max:255',
            'timezone' => 'required|string|max:255',
        );

        $messages = [
            'name.required' => '* Name field is required',
            'name.string' => '* Enable browser notification field is required',
            'name.max' => '* Name field is too long',

            'website.required' => '* Website field color field is required',
            'website.url' => '* Website is not a valid http address',
            'website.max' => '* Website field is too long',

            'country.required' => '* Country field is required',
            'country.string' => '* Country field is invalid',
            'country.max' => '* Country filed is too long',

            'timezone.required' => '* Timezone field is required',
            'timezone.string' => '* Timezone field is invalid',
            'timezone.max' => '* Timezone filed is too long',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();
            $organizationSettings = OrganizationSettings::where("id", $userOrganization->organization_id)->first();
            $organizationSettings->name = $request->name;
            $organizationSettings->timezone = $request->timezone;
            $organizationSettings->website = $request->website;
            $organizationSettings->country = $request->country;
            $organizationSettings->save();

            return response()->json([
                SuccessStatus::DATA => "Organization settings updated successfully"
            ]);
        }
    }
}
