<?php
namespace App\Services\Admin;

use App\Models\Admin;
use App\Traits\FileUpload;
use App\Traits\Response;
use Auth;
use Exception;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class AdminAuthService
{

    use Response;
    use FileUpload;

    public function adminSignup($data)
    {
        try {
            $admin = new Admin();
            $admin->first_name = $data['first_name'];
            $admin->last_name = isset($data['last_name']) ? $data['last_name'] : null;
            $admin->phone_number = $data['phone_number'];
            $admin->email = $data['email'];
            $admin->password = Hash::make($data['password']);
            $admin->save();
            $access_token = $admin->createToken('authToken')->plainTextToken;
            $data = [
                'id' => $admin->id,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'phone_number' => $admin->phone_number,
                'email' => $admin->email,
                'access_token' => $access_token,
            ];
            return $this->success(false, "Admin successfully registered!", $data);
        } catch (\Exception$e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't registered admin. $error ");
        }
    }

    public function adminLogin($data)
    {
        try {
            $admin = Admin::where('email', $data['email'])->first();
            if ($admin) {
                $checkPassword = Hash::check($data['password'], $admin->password);
                if ($checkPassword) {
                    $token = $admin->createToken('authToken')->plainTextToken;
                    $data = [
                        'admin_id' => $admin->id,
                        'first_name' => $admin->first_name,
                        'last_name' => $admin->last_name,
                        'phone_number' => $admin->phone_number,
                        'email' => $admin->email,
                        'access_token' => $token,
                    ];
                    return $this->success(false, "Admin successfully logged in!", $data);
                }
                return $this->fail(true, "Incorrect password!", '');
            }
            return $this->fail(true, "Incorrect email address !", '');
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't logged in admin: $error !", $e->getMessage());
        }
    }

    public function userProfile()
    {
        try {

            $user = Admin::where('id', auth()->user('admin')->id)->first();

            return $this->success(false, "User successfully fetched!", $user);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't fetched user: $error!", $e->getMessage());
        }
    }

    public function updateAdminProfile($data)
    {
        try {

            $user = Auth::user('admin');
            $user->first_name = $data['first_name'];
            $user->last_name = isset($data['last_name']) ? $data['last_name'] : null;
            $user->phone_number = $data['phone_number'];
            $user->save();
            return $this->success(false, "User profile successfully updated!", $user);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't update user profile: $error!", $e->getMessage());
        }
    }

    public function adminLogout()
    {
        try {
            $user = Auth::user('admin')->currentAccessToken()->delete();

            return $this->success(false, "Admin successfully logged out!", $user);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't logged out user: $error!", $e->getMessage());
        }
    }

    public function changeAdminPassword($data)
    {
        try {

            $admin = Admin::find(auth()->user('admin')->id);

            $check = Hash::check($data['old_password'], $admin->password);

            if ($check) {

                $admin->password = Hash::make($data['password']);
                $admin->save();

                return $this->success(false, "Password successfully changed!!", '');
            } else {
                return $this->fail(true, 'Incorrect old password!', '');
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't changed admin's password: $error!", $e->getMessage());

        }
    }

}
