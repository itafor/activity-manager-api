<?php
namespace App\Services\User;

use App\Mail\SendPasswordResetToken;
use App\Mail\sendTestEmailHandler;
use App\Models\User;
use App\Traits\Common;
use App\Traits\Response;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 *
 */
class PasswordResetService
{
    use Response, Common;

    public function sendPasswordResetOTP($email)
    {
        try {

            $user = User::where('email', $email)->first();

            if (!$user) {

                return $this->fail(true, "User does not exist!", null, 400);
            }

            $token = $this->createPasswordResetToken($email);

            $tokenData = DB::table('password_resets')
                ->where('email', $email)
                ->where('user_type', 'User')->first();

            $email = $user->email;

            Mail::to($email)->send(new SendPasswordResetToken($user, $tokenData->token));

            return $this->success(false, "We’ve sent a pasword recovery otp to your email address. (" . $email . ")", $email, 200);

        } catch (\Exception$e) {
            return $this->fail(true, "Couldn't send pasword recovery otp. Please try again!", $e->getMessage(), 400);

        }
    }

    public function createPasswordResetToken($email)
    {

        $token = DB::table('password_resets')
            ->where('email', $email)
            ->where('user_type', 'User')->first();

        if ($token) {
            DB::table('password_resets')
                ->where('email', $email)
                ->where('user_type', 'User')->update(['token' => $this->generateFourDigitsOTP()]);

        } else {

            $reset = DB::table('password_resets')->insert(
                [
                    'email' => $email,
                    'user_type' => 'User',
                    'token' => $this->generateFourDigitsOTP(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

    }

     public function createPhonePasswordResetToken($phone_number)
    {

        $token = DB::table('password_resets')
            ->where('phone_number', $phone_number)
            ->where('user_type', 'User')->first();

        if ($token) {
            DB::table('password_resets')
                ->where('phone_number', $phone_number)
                ->where('user_type', 'User')->update(['token' => $this->generateFourDigitsOTP()]);

        } else {

            $reset = DB::table('password_resets')->insert(
                [
                    'phone_number' => $phone_number,
                    'user_type' => 'User',
                    'token' => $this->generateFourDigitsOTP(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

    }

    public function resetUserPassword(array $data)
    {
        try {

            $password = $data['password'];
            $token = $data['otp'];

            $tokenData = DB::table('password_resets')
                ->where('token', $token)
                ->where('user_type', 'User')->first();

            if (!$tokenData) {
                return $this->fail(true, "Invalid otp!", null, 400);
            }

            $user = User::where('email', $tokenData->email)->orWhere('phone_number', $tokenData->phone_number)->first();

            if ($user) {
                $this->updateUserPassword($user, $tokenData, $password);

                return $this->success(false, "Password successfully changed!", $token, 200);

            } else {
                return $this->fail(true, "Email not found!", null, 400);
            }

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't reset password. Please try again!", $e->getMessage(), 400);
        }

    }

    public function updateUserPassword($user, $tokenData, $password)
    {
        try {
            $user->password = Hash::make($password);
            $user->save();

            DB::table('password_resets')->where('token', $tokenData->token)
                ->delete();

        } catch (Exception $e) {
            return $this->fail(true, "Couldn't send password reset success email!", $e->getMessage(), 400);
        }
    }

    public function verifyOTP($data)
    {
        try {

            $otp = $data['otp'];

            $userOtp = DB::table('password_resets')
                ->where('token', $otp)
                ->where('user_type', 'User')->first();

            if ($userOtp) {

                return $this->success(false, "OTP successfully verified!", $otp, 200);

            } else {
                return $this->fail(true, "Invalid OTP!", $otp, 400);
            }
        } catch (Exception $e) {
            return $this->fail(true, "Couldn't verify OTP!", $e->getMessage(), 400);
        }

    }
}
