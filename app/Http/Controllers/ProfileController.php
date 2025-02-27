<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorStatus;
use App\Helpers\SuccessStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nette\Utils\Image;

class ProfileController extends Controller
{

    public function updateAccount(Request $request){
        $user = User::where('id', auth()->user()->id)->first();
        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,id,'.$user->id,
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
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return response()->json([SuccessStatus::MESSAGE => "Profile updated successfully"]);
        }
    }
    public function useRandomAvatar(){
        $randomString = Str::random();
        $apiUrl = "https://api.dicebear.com/7.x/adventurer/svg?seed=`{$randomString}`";
        $filename = "avatar_" . time() . ".svg";
        $fileDir = 'storage/user/';

        $savePath = $fileDir . $filename;
        $fileContent = file_get_contents($apiUrl);

        if (!$fileContent) {
            $file = "";
        }

        file_put_contents($savePath, $fileContent);
        $file = asset($fileDir . $filename);

        return response()->json([
            SuccessStatus::MESSAGE => 'Profile Image updated successfully',
            'avatar' => asset($file) // Public URL
        ]);
    }
    public function removeAvatar(Request $request){
        $user = User::where('id', auth()->user()->id)->first();
        $fileDir = 'storage/user/';
        $filename = "avatar.svg";

        $user->avatar = $filename;
        $user->save();

        $file = asset($fileDir . $filename);

        return response()->json([
            SuccessStatus::MESSAGE => 'Profile Image updated successfully',
            'avatar' => asset($file) // Public URL
        ]);

    }
    public function uploadAvatar(Request $request){
        $rules = array(
            'avatar' => 'required|image|mimes:jpg,png,jpeg|max:20048',
        );

        $messages = [
            'avatar.required' => '* Please upload a file',
            'avatar.image' => '* Invalid file uploaded',
            'avatar.mimes' => '* File must be of the following extension jpg,png,jpeg,gif,svg',
            'avatar.max' => '* File must be not be over 50mb',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([ErrorStatus::ERROR => $validator->errors()], 422);
        } else {
            $user = User::where('id', auth()->user()->id)->first();
            $fileDir = 'storage/user/';


            $image = $request->file('avatar');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = public_path( $fileDir . $filename);
            $img = Image::fromFile($image->getPathname());
            $img->resize(200, null);
            $img->save($path, 30);

            $user->avatar = $filename;
            $user->save();
            $file = asset($fileDir . $filename);

            return response()->json([
                SuccessStatus::MESSAGE => 'Profile Image updated successfully',
                'avatar' => asset($file) // Public URL
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        $rules = array(
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
            'new_password_confirmation' => 'required|max:120|min:6|same:new_password',

        );
        $messages = [
            'current_password.required' => 'This field is required',
            'current_password.string' => 'Invalid Characters',

            'new_password.required' => 'This field is required',
            'new_password.string' => 'Invalid Characters',
            'new_password.min' => 'Password must be minimum of 6 characters',

            'new_password_confirmation.required' => 'This field is required',
            'new_password_confirmation.string' => 'Invalid Characters',
            'new_password_confirmation.min' => 'Password must be minimum of 6 characters',
            'new_password_confirmation.same' => 'Password confirmation does not match',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else {
            $user = User::where('id', auth()->user()->id)->first();
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([ErrorStatus::ERROR => ['current_password' => ['Current password is incorrect']]], 422);
            } else {
                $user->password = Hash::make($request->new_password);
                $user->save();

                return response()->json([
                    SuccessStatus::MESSAGE => 'Password updated successfully',
                ]);
            }
        }
    }
}
