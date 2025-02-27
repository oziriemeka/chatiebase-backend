<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Http\Resources\AvaialablePrivilegeResource;
use App\Http\Resources\PrivilegeResource;
use App\Models\Permission;
use App\Models\Privilege;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionsController extends Controller
{
    public function getPermissions()
    {
        $permission = Permission::all()->groupBy('group');
        return response()->json(['data' => $permission]);
    }

    public function getAvailablePrivilege()
    {
        $userOrganization  = UserOrganization::where('user_id', auth()->user()->id)->first();
        $privilege = Privilege::where('organization_id', $userOrganization->organization_id)->orderBy("created_at", "desc")->get();
        return response()->json([
            SuccessStatus::DATA => AvaialablePrivilegeResource::collection($privilege)
        ]);
    }
    public function addPermissions(Request $request){

        $rules = array(
            'permission_name' => 'required|string|max:255',
            'selected_permissions' => 'nullable|array',
        );

        $messages = [
            'permission_name.required' => '* Name is required',
            'permission_name.string' => '* Invalid characters',
            'permission_name.max' => '* name is too long',

            'selected_permissions.array' => '* Invalid data',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {

            $org = UserOrganization::where('user_id', auth()->user()->id)->first();
            $privilege = new Privilege();
            $privilege->name = $request->permission_name;
            $privilege->organization_id = $org->organization_id;
            $privilege->save();

            $privilege->permissions()->sync($request->selected_permissions);

            return response()->json([
                SuccessStatus::MESSAGE => "Privilege added successfully",
                SuccessStatus::DATA => new PrivilegeResource($privilege)
            ]);
        }
    }

    public function getCustomPrivilege(Request $request){
        $userOrganization  = UserOrganization::where('user_id', auth()->user()->id)->first();
        $privilege = Privilege::where('organization_id', $userOrganization->organization_id)->orderBy("created_at", "desc")->get();
        return response()->json([
            SuccessStatus::DATA => PrivilegeResource::collection($privilege)
        ]);
    }


    public function deleteCustomPrivilege($permissionId){
        $userOrganization  = UserOrganization::where('user_id', auth()->user()->id)->first();
        $privilege = Privilege::where('id', $permissionId)->where('organization_id', $userOrganization->organization_id)->firstOrFail();
        if($privilege->can_delete){
            $privilege->delete();
            return response()->json([
                SuccessStatus::MESSAGE => "Privilege deleted successfully"
            ]);
        } else {
            return response()->json([
                ErrorStatus::REQUEST_INVALID => "Can not delete default permission"
            ], 422);
        }
    }

    public function editCustomPrivilege(Request $request, $permissionId)
    {
         $rules = array(
            'permission_name' => 'required|string|max:255',
            'selected_permissions' => 'nullable|array',
        );

        $messages = [
            'permission_name.required' => '* Name is required',
            'permission_name.string' => '* Invalid characters',
            'permission_name.max' => '* name is too long',

            'selected_permissions.array' => '* Invalid data',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $userOrganization  = UserOrganization::where('user_id', auth()->user()->id)->first();
            $privilege = Privilege::where('id', $permissionId)->where('organization_id', $userOrganization->organization_id)->firstOrFail();

            $privilege->name = $request->permission_name;
            $privilege->permissions()->sync($request->selected_permissions);
            $privilege->save();

            return response()->json([
                SuccessStatus::MESSAGE => "Privilege and permission updated successfully",
                SuccessStatus::DATA =>  new PrivilegeResource($privilege)
            ]);
        }
    }

}
