<?php
namespace App\Services\User;

use App\Models\User;
use App\Traits\FileUpload;
use App\Traits\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class UserAuthService
{

    use Response;
    use FileUpload;

    public function userSignup($request)
    {
        try {

            $user = new User();
            $user->full_name = $request->full_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();


            $access_token = $user->createToken('authToken')->plainTextToken;

            $data = [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'access_token' => $access_token,
            ];


            return $this->success(false, "User successfully registered!", $data);

        } catch (\Exception$e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't register user. $error ");
        }
    }


     public function userlogin($data)
    {
        try {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                $checkPassword = Hash::check($data['password'], $user->password);
                if ($checkPassword) {
                    $token = $user->createToken('authToken')->plainTextToken;
                   $data = [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'access_token' => $token,
                    ];
                    return $this->success(false, "User successfully logged in!", $data);
                }
                return $this->fail(true, "Incorrect password!", '');
            }
            return $this->fail(true, "Incorrect email address", '');
        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't log in user: $error !", $e->getMessage());
        }
    }


    public function userProfile()
    {
        try {

            $user = User::where('id', auth()->user()->id)->first();

            return $this->success(false, "User successfully fetched!", $user);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't fetched user: $error!", $e->getMessage());
        }
    }
   

    public function userLogout()
    {
        try {
            $user = Auth::user('user')->currentAccessToken()->delete();

            return $this->success(false, "User successfully logged out!", $user);

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't logged out user: $error!", $e->getMessage());
        }
    }

    public function changeUserPassword($data)
    {
        try {

            $user = User::find(auth()->user()->id);

            $check = Hash::check($data['old_password'], $user->password);

            if ($check) {

                $user->password = Hash::make($data['password']);
                $user->save();

                return $this->success(false, "Password successfully changed!!", '');
            } else {
                return $this->fail(true, 'Incorrect old password!', '');
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't changed user's password: $error!", $e->getMessage());

        }
    }

}
