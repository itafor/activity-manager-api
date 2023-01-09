<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserSignupRequest;
use App\Services\User\UserAuthService;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
     public $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function userSignup(UserSignupRequest $request)
    {
        $validated = $request->validated();
       
        return $this->userAuthService->userSignup($request);
    }


    public function userlogin(UserLoginRequest $request)
    {
        $validated = $request->validated();
        return $this->userAuthService->userlogin($validated);
    }

    public function userProfile()
    {
        return $this->userAuthService->userProfile();
    }

    public function userLogout()
    {
        return $this->userAuthService->userLogout();
    }

    public function changeUserPassword(Request $request)
    {
        $validated = $request->validate([
            "old_password" => "required|string",
            "password" => "required|confirmed|string",
        ]);
        return $this->userAuthService->changeUserPassword($validated);

    }
}
