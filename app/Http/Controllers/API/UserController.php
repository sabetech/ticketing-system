<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class UserController extends BaseController
{
    //
    public function getAllUsers() {

        $users = User::withTrashed()->with(['roles'])->get();

        return $this->sendResponse($users, 'Users retrieved successfully.');
    }
}
