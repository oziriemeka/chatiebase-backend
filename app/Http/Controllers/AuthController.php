<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Mail\ResetPassword;
use App\Models\Country;
use App\Models\GeneralSettings;
use App\Models\OrganizationSettings;
use App\Models\PasswordReset;
use App\Models\Permission;
use App\Models\Privilege;
use App\Models\User;
use App\Models\UserApplicationSettings;
use App\Models\UserOrganization;
use App\Services\GuzzleClient;
use Carbon\Carbon;
use DateTimeZone;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

const FALLBACK_COUNTRY = "US";
const FALLBACK_TIMEZONE = "America/New_York";
const FALLBACK_LOCATION = [
    "country_code" => FALLBACK_COUNTRY,
    "timezone" => FALLBACK_TIMEZONE
];
class AuthController extends Controller
{
    public function register(Request $request)
    {
        if($request->registration_stage === "BASIC_REGISTRATION"){
            return $this->validateRegistrationInput($request);
        } else {
            return $this->processRegistration($request);
        }

    }

    private function validateRegistrationInput(Request $request)
    {


        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        );

        $messages = [
            'name.required' => '* Name is required',
            'name.string' => '* Invalid characters',
            'name.max' => '* name is too long',

            'email.required' => '* Your email is required',
            'email.string' => '* Invalid characters',
            'email.email' => '* Must be of email format with \'@\' symbol',
            'email.max' => '* Email is too long',
            'email.unique' => 'This email already exist',

            'password.required' => 'Please enter a password',
            'password.string' => 'Invalid characters',
            'password.min' => 'Password must be minimum of 6 characters',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            return response()->json([
                "data" => [
                    'active_information' => $this->getUserLocationRaw($request),
                    'timezones' => $this->getTimeZone(),
                    'countries' => Country::get()
                ]
            ]);
        }
    }

    private function processRegistration(Request $request)
    {
        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'company_name' => 'required|string|max:255',
            'website' => 'required|string|url|max:255|unique:organization_settings',
            'country' => 'required|max:255',
            'timezone' => 'required|string|max:255',
        );

        $messages = [
            'name.required' => '* Name is required',
            'name.string' => '* Invalid characters',
            'name.max' => '* name is too long',

            'email.required' => '* Your email is required',
            'email.string' => '* Invalid characters',
            'email.email' => '* Must be of email format with \'@\' symbol',
            'email.max' => '* Email is too long',
            'email.unique' => 'This email already exist',

            'password.required' => 'Please enter a password',
            'password.string' => 'Invalid characters',
            'password.min' => 'Password must be minimum of 6 characters',

            'company_name.required' => '* Company Name is required',
            'company_name.string' => '* Invalid characters',

            'website.required' => '* Company Website is required',
            'website.string' => '* Invalid characters',
            'website.url' => '* Invalid URL',

            'country.required' => '* Country is required',

            'timezone.required' => '*  Timezone is required',
            'timezone.string' => '* Timezone is invalid',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {

            try {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();

                $organizationSettings = new OrganizationSettings();
                $organizationSettings->name = $request->company_name;
                $organizationSettings->website = $request->website;
                $organizationSettings->timezone = $request->timezone;
                $organizationSettings->country = $request->country['name'];
                $organizationSettings->save();

                $userOrganization = new UserOrganization();
                $userOrganization->user_id = $user->id;
                $userOrganization->organization_id = $organizationSettings->id;
                $userOrganization->save();

                $this->createDefaultRolesAndPermission($userOrganization);
                $this->createUserApplicationSettings($user->id);
                $token = Auth::login($user);

                return response()->json([
                    'access_token' => $token,
                    'type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                    'name' => auth()->user()->name,
                    'email' => $user->email,
                    'avatar' => asset(auth()->user()->avatar),
                    'user_id' => $user->id,
                ]);
            } catch(\Exception $ex){
                return response()->json([ErrorStatus::SYSTEM_ERROR => $ex->getMessage()]);
            }

        }
    }

    private function createDefaultRolesAndPermission(UserOrganization $userOrg)
    {
        $permissions = Permission::all()->pluck('id');
        $name = "Administrator";
        $privilege = new Privilege();
        $privilege->name = $name;
        $privilege->can_delete = 0;
        $privilege->organization_id = $userOrg->organization_id;
        $privilege->save();
        $privilege->permissions()->sync($permissions); //Assign all privilege to Administrator

        unset($permissions);
        unset($name);
        unset($privilege);

        $permissions = Permission::where("group", "chat")->get()->pluck('id');

        $name = "Customer care representative";
        $privilege = new Privilege();
        $privilege->name = $name;
        $privilege->can_delete = 0;
        $privilege->organization_id = $userOrg->organization_id;
        $privilege->save();
        $privilege->permissions()->sync($permissions); //Assign all privilege to Administrator
    }
    public function login(Request $request)
    {

        $rules = array(
            'email' => 'required|string|email',
            'password' => 'required|string',
        );
        $messages = [
            'email.required' => '* Your Email is required',
            'email.string' => '* Invalid Characters',
            'email.email' => '* Must be of Email format with \'@\' symbol',

            'password.required'   => 'This field is required',
            'password.string'   => 'Invalid Characters',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $credentials = request(['email', 'password']);
            $token = auth()->attempt($credentials);
            if ($token) {
                return $this->respondWithToken($token);
            } else {
                return response()->json([ErrorStatus::ERROR => [ErrorStatus::AUTHENTICATION_ERROR => ['These credentials do not match our records. ']]], 422);
            }
        }
    }
    public function check()
    {
        if (Auth::check()) {
            return response()->json([ SuccessStatus::DATA => true ]);
        } else {
            return response()->json([ SuccessStatus::DATA => false ]);
        }
    }
    public function logout()
    {
        Auth::logout();
        return response()->json([
            SuccessStatus::SUCCESS => 'success'
        ]);
    }
    public function respondWithToken($token)
    {
        $user = User::where("id", Auth::user()->id)->first();
        $user->last_login = Carbon::now();
        $user->save();

        return response()->json([
            'user_id' => $user->id,
            'access_token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'permission' => $user->privilege->name
        ]);
    }
    public function resetPassword(Request $request)
    {
        $email = $request->email;
        $rules = [
            'email' => 'required|email|max:120|string',
        ];

        $messages = [
            'email.required' => 'This field is required',
            'email.max' => 'This email is too long or invalid',
            'email.string' => 'Please enter a valid input',
            'email.email' => 'Please enter a valid email address',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            if (User::where('email', $email)->exists() == true) {
                $user = User::where('email', $email)->first();
                $settings = GeneralSettings::first();
                if ($settings) {
                    PasswordReset::where('email', $user->email)->delete();
                    $token = Str::random('100');
                    $reset_password = new PasswordReset();
                    $reset_password->email = $user->email;
                    $reset_password->token = $token;
                    $reset_password->save();
                    try {
                        Mail::to($user)->send(new ResetPassword($user, $token));
                    } catch (\Exception $ex) {
                        return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Sorry, our system is unable to reset your account password at this time, please try again later thank you.']]], 422);
                    }
                    return response()->json([SuccessStatus::MESSAGE => "If there is any account associated with your email address {$email}, you should receive a password reset email shortly"], 200);
                } else {
                    return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Sorry, our system is unable to reset your account password at this time, please try again later thank you.']]], 422);
                }
            } else {
                return response()->json([SuccessStatus::MESSAGE => "If there is any account associated with your email {$email}, you should receive a password reset email shortly"], 200);
            }
        }
    }
    public function resetPasswordCheckToken($token)
    {
        $resetPasswordToken = PasswordReset::where('token', $token)->first();
        if ($resetPasswordToken) {
            return response()->json([SuccessStatus::DATA => "Valid reset token"], 200);
        } else {
            return response()->json([ErrorStatus::REQUEST_INVALID => "Invalid reset token"], 404);
        }
    }
    public function resetPasswordToken(Request $request, $token)
    {
        $token = $request->token;
        if (PasswordReset::where('token', $token)->exists()) {
            $rules = [
                'new_password' => 'required|max:120|min:6',
                'new_password_confirmation' => 'required|max:120|min:6|same:new_password',
            ];

            $messages = [
                'new_password.required' => 'This field is required',
                'new_password.max' => 'Password is too long, please pick something you would remember',
                'new_password.min' => 'Password must be 6 characters long',

                'new_password_confirmation.required' => 'This field is required',
                'new_password_confirmation.max' => 'Password is too long, please pick something you would remember',
                'new_password_confirmation.min' => 'Password must be 6 characters long',
                'new_password_confirmation.same' => 'Password confirmation does not match',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
            } else {
                $data = PasswordReset::where('token', $token)->OrderBy('created_at', 'DESC')->first();
                $user = User::where('email', $data->email)->first();
                $user->update([
                    'password' => Hash::make($request->new_password)
                ]);
                PasswordReset::where('email', $user->email)->delete();
                return response()->json([SuccessStatus::MESSAGE => 'Your account password has been changed successfully, please login to continue.'], 200);
            }
        } else {
            return response()->json(['error' => [ErrorStatus::SYSTEM_ERROR => ['Sorry, our system is unable to reset your account password at this time, please try again later thank you.']]], 422);
        }
    }

    function getUserLocationRaw($request)
    {
        $ip = $request->ip();

        if ($ip == "127.0.0.1" || $ip == "::1") {
            return FALLBACK_LOCATION;
        }

        $client = new GuzzleClient();
        try {
            $response = $client->get("http://www.geoplugin.net/json.gp?ip={$ip}");
            $data = json_decode($response->getBody()->getContents(), true);

            if(isset($data['geoplugin_countryCode'] ) && isset($data['geoplugin_timezone'])) {
                return  [
                    'country' => [
                        "name" => $data['geoplugin_countryName'],
                        "code" => $data['geoplugin_countryCode']
                    ],
                    'timezone' => $data['geoplugin_timezone'],
                ];
            } else {
                return FALLBACK_LOCATION;
            }

        } catch (\Exception $e) {
            //Todo : Log Error $e->getMessage()
        } catch (GuzzleException $e) {
            //Todo : Log Guzzle related error $e->getMessage()
        }
    }

    public function getTimeZone()
    {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    private function createUserApplicationSettings($userId){
        $applicationSettings = new UserApplicationSettings();
        $applicationSettings->user_id = $userId;
        $applicationSettings->new_chat_sound = "1";
        $applicationSettings->existing_chat_sound = "1";
        $applicationSettings->new_website_visitor_sound = "1";
        $applicationSettings->enable_sound_for_new_visitor = "1";
        $applicationSettings->save();
    }
}
