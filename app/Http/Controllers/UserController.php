<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\Response;


class UserController extends Controller
{
     use Response;

       public function getUsers()
    {
        try {
            
        	$users = User::with('activities')->orderBy('id','desc')->get();

            return $this->success(false, "All users!", $users);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't fetch users! $error", "");
        }
    }


  public function getOneUser($userId)
    {
        try {
            
        	$user = User::where('id', $userId)->with('activities')->first();

            return $this->success(false, "User!", $user);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't fetch user! $error", "");
        }
    }

}
