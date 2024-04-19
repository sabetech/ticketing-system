<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;

class UserController extends BaseController
{
    //
    public function getAllUsers() {

        $users = User::withTrashed()->with(['roles'])->get();

        return $this->sendResponse($users, 'Users retrieved successfully.');
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

}
