<?php

namespace App\Http\Controllers;

use App\Services\Admin\AdminAuthService;
use App\Traits\FileUpload;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    use FileUpload;

    public $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function adminSignup(Request $request)
    {
        $validated = $request->validate([
            "first_name" => "required|string",
            "last_name" => "nullable",
            "email" => "required|email|string|unique:admins",
            "phone_number" => "required|string|unique:admins",
            "password" => "required|confirmed|string|min:8",
        ]);

        return $this->adminAuthService->adminSignup($validated);
    }

    public function adminLogin(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email|exists:admins|string",
            "password" => "required|string",
        ]);

        return $this->adminAuthService->adminLogin($validated);
    }

    public function adminProfile()
    {
        return $this->adminAuthService->userProfile();
    }

    public function updateAdminProfile(Request $request)
    {
        $validated = $request->validate([
            "first_name" => "required|string",
            "last_name" => "nullable",
            "phone_number" => "nullable",
        ]);
        return $this->adminAuthService->updateAdminProfile($validated);
    }


    public function adminLogout()
    {
        return $this->adminAuthService->adminLogout();
    }

    public function changeAdminPassword(Request $request)
    {
        $validated = $request->validate([
            "old_password" => "required|string",
            "password" => "required|confirmed|string",
        ]);
        return $this->adminAuthService->changeAdminPassword($validated);

    }

}
