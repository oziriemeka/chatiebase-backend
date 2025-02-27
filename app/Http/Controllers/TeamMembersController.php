<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Http\Resources\TeamMemberResource;
use App\Mail\InviteTeamMember;
use App\Models\Privilege;
use App\Models\User;
use App\Models\UserApplicationSettings;
use App\Models\UserOrganization;
use App\Models\UserPrivilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamMembersController extends Controller
{

    public function getTeamMember(Request $request)
    {
        $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();
        if(!$userOrganization){
            return response()->json([
                SuccessStatus::DATA => []
            ]);
        } else {
          $teamMembersId =  UserOrganization::where('organization_id', $userOrganization->organization_id)->get()->pluck('user_id');
          $teamMembers = User::whereIn("id", $teamMembersId)->orderBy("created_at", "Desc")->get();
          return  response()->json([
              SuccessStatus::DATA => TeamMemberResource::collection($teamMembers)
          ]);
        }
    }

    public function deleteSelectedTeamMember(Request $request){
        $activeUserOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();

        $userOrganization = UserOrganization::whereIn('user_id', $request->ids)->where("organization_id", $activeUserOrganization->id)->get();
        if($userOrganization){
           User::whereIn('id', $request->ids)->delete();
            return response()->json([
                SuccessStatus::DATA => "Selected Team member deleted successfully"
            ]);
        } else {
            return response()->json([ErrorStatus::REQUEST_INVALID => "Invalid request, unable to continue"]);
        }
    }

    public function deleteTeamMember($userId)
    {
        $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();
        $activeUserOrganization = UserOrganization::where('user_id', $userId)->first();

        //Todo: and has delete team member permission
        if($userOrganization->organization_id == $activeUserOrganization->organization_id){
            $user = User::where("id", $userId)->firstOrFail();
            $user->delete();

            return response()->json([
                SuccessStatus::DATA => "Team member deleted successfully"
            ]);
        } else {
            return response()->json([ErrorStatus::REQUEST_INVALID => "Invalid request, unable to continue"]);
        }
    }
    public function searchTeamMember(Request $request)
    {
        $searchString = $request->term;
        $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();
        $users = User::leftJoin('user_organizations', 'users.id', '=', 'user_organizations.user_id')
            ->where('user_organizations.organization_id', $userOrganization->organization_id)
            ->where(function ($query) use ($searchString) {
                $query->where('users.name', 'like', '%' . $searchString . '%')
                    ->orWhere('users.email', 'like', '%' . $searchString . '%');
            })
            ->orderBy('users.created_at', 'desc')
        ->get();

        return response()->json([
            SuccessStatus::DATA => TeamMemberResource::collection($users)
        ]);
    }
    public function editTeamMember(Request $request, $userId)
    {
        $user = User::where('id', $userId)->first();
        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,id,'.$user->id,
            'permission' => 'required|numeric',
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

            'permission.required' => 'User permission is required',
            'permission.string' => 'Invalid characters',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $userOrganization = UserOrganization::where('user_id', auth()->user()->id)->first();
            $activeUserOrganization = UserOrganization::where('user_id', $userId)->first();

            //Todo: and has delete team member permission
            if($userOrganization->organization_id != $activeUserOrganization->organization_id){
                return response()->json([ErrorStatus::REQUEST_ERROR => "Sorry unable to perform operation at this time"]);
            }


            $user = User::where("id", $userId)->firstOrFail();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();


            if(!UserPrivilege::where('privilege_id', $request->permission)->exists()){
                return response()->json([ErrorStatus::REQUEST_ERROR => "Sorry unable to perform operation at this time"]);
            }

            $privilege = UserPrivilege::where('user_id', $user->id)->first();
            if(!$privilege){
                return response()->json([ErrorStatus::REQUEST_ERROR => "Sorry unable to perform operation at this time"]);
            }

            $privilege->privilege_id = $request->permission;
            $privilege->save();

            return response()->json([
                SuccessStatus::MESSAGE => "Team member updated successfully",
                SuccessStatus::DATA => new TeamMemberResource($user)
            ]);

        }
    }

    public function addTeamMember(Request $request)
    {

        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'permission' => 'required|numeric',
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

            'permission.required' => 'User permission is required',
            'permission.string' => 'Invalid characters',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $organizationId = UserOrganization::where("user_id", auth()->user()->id)->firstOrFail();
            $privilege = Privilege::where("id", $request->permission)->firstOrFail();

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->was_invited = 1;
            $user->was_invited_by = auth()->user()->id;
            $user->email_verification_token = Str::random('100');
            $user->password = Hash::make(Str::random('100'));
            $user->email_verification_token_at = now();
            $user->save();

            $userPrivilege = new UserPrivilege();
            $userPrivilege->user_id = $user->id;
            $userPrivilege->privilege_id = $privilege->id;
            $userPrivilege->save();

            $userOrganization = new UserOrganization();
            $userOrganization->user_id = $user->id;
            $userOrganization->organization_id = $organizationId->organization_id;
            $userOrganization->save();

            $applicationSettings = new UserApplicationSettings();
            $applicationSettings->user_id = $user->id;
            $applicationSettings->new_chat_sound = "1";
            $applicationSettings->existing_chat_sound = "1";
            $applicationSettings->new_website_visitor_sound = "1";
            $applicationSettings->enable_sound_for_new_visitor = "1";
            $applicationSettings->save();


            // Send Email
            // Add Emails to queue
            try {
                Mail::to($user)->send(new InviteTeamMember($user, $userOrganization, $user->email_verification_token));
            } catch (\Exception $ex) {
                return response()->json([ErrorStatus::ERROR => [ErrorStatus::SYSTEM_ERROR => ['Sorry, our system is unable to perform this action at this time, please try again later thank you.']]], 422);
            }
            $user = User::where("id", $user->id)->first();
            return response()->json(
                [
                    SuccessStatus::MESSAGE => "User invited successfully",
                    SuccessStatus::DATA => new TeamMemberResource($user)
                ]);
        }
    }
}
