<?php

namespace App\Http\Controllers;

use App\Helpers\SuccessStatus;
use App\Helpers\UtilityHelper;
use App\Http\Resources\ChatSoundResource;
use App\Http\Resources\UserApplicationSettingsResource;
use App\Models\ApplicationChatSound;
use App\Models\ApplicationSettings;
use App\Models\UserApplicationSettings;
use Illuminate\Http\Request;

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

    public function updateUserApplicationSettings()
    {

    }
}
