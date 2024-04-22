<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Storage;
use Validator;
use Log;

class UserController extends BaseController
{
    //
    public function getAllUsers() {

        $users = User::withTrashed()->with(['roles'])->get();

        return $this->sendResponse($users, 'Users retrieved successfully.');
    }

    public function createUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'username' => 'required|unique:users,username',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();

        Log::info($input);

        $input['password'] = bcrypt($input['password']);

        $filename = "";
        if ($request->hasFile('user_image')) {
            $file = $request->file('user_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put($filename, file_get_contents($file));
        }else {
            $filename = "https://www.kindpng.com/picc/m/24-248253_user-profile-default-image-png-clipart-png-download.png";
        }

        $input['photo'] = $filename;

        unset($input['role']);
        $user = User::create($input);
        $user->assignRole($request->get('role'));

        return $this->sendResponse($user, 'User created successfully.');
    }

    public function deleteUser($id, Request $request) {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return $this->sendResponse($user, 'User deleted successfully.');
        }else {
            return $this->sendError("User Not Found");
        }
    }

    public function roles(Request $request) {

        $roles = Role::select('id', 'name')->get();

        return $this->sendResponse($roles, 'Roles retrieved successfully.');

    }

}
