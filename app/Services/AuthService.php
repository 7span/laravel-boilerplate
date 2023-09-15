<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Arr;
use App\Jobs\ChangePassword;
use App\Jobs\ForgetPasswordMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private $userObj;

    private $userOtpObj;

    private $userOtpService;

    public function __construct(User $userObj)
    {
        $this->userObj = $userObj;
        $this->userOtpObj = new UserOtp();
        $this->userOtpService = new UserOtpService($this->userOtpObj);
    }

    public function signup($inputs)
    {
        $user = $this->userObj->create(Arr::only($inputs, ['name', 'email', 'password']));

        return $user;
    }

    public function login($inputs)
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (! $user || ! Hash::check($inputs['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return $user;
    }

    public function forgetPassword($inputs)
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();
        if ($user == null) {
            $data['errors']['email'][] = __('message.emailNotExist');

            return $data;
        }
        $this->userOtpObj->where('user_id', $user['id'])->where('otp_for', 'forget_password')->delete();

        $otp = mt_rand(1000, 9999);
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'forget_password']);

        ForgetPasswordMail::dispatch($user, $otp)->onQueue('email');
        $data['message'] = __('message.forgetPasswordEmailSuccess');

        return $data;
    }

    public function resetPassword($inputs)
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();
        if ($user == null) {
            $data['errors']['email'][] = __('message.emailNotExist');

            return $data;
        }

        $userOtp = $this->userOtpObj->where('user_id', $user['id'])->where('otp', $inputs['otp'])->where('otp_for', 'forget_password')->first();
        if ($userOtp == null) {
            $data['errors']['otp'][] = __('message.invalidOtp');

            return $data;
        }
        $expirationTime = config('site.otpExpirationTimeInMinutes');
        $expirationDate = Carbon::parse($userOtp['created_at'])->addMinutes($expirationTime)->format('Y-m-d H:i:s');

        if ($userOtp['used_at'] != null || date('Y-m-d h:i:s') > $expirationDate) {
            $data['errors']['otp'][] = __('message.invalidOtp');

            return $data;
        }
        $this->userOtpService->update($userOtp['id'], ['used_at' => date('Y-m-d h:i:s')]);
        $user->password = $inputs['password'];
        $user->save();
        $data['message'] = __('message.passwordChangeSuccess');
        $data['user'] = $user;

        return $data;
    }

    public function changePassword($inputs)
    {
        $currentPassword = trim($inputs['current_password']);
        $newPassword = trim($inputs['new_password']);

        $user = Auth::user();

        if (strcmp($currentPassword, $newPassword) == 0) {
            // Current password and new password same
            $data['errors']['message'] = __('message.newPasswordMatchedWithCurrentPassword');

            return $data;
        }

        $user->password = $newPassword;
        $user->save();

        try {
            ChangePassword::dispatch($user);
        } catch (\Exception $e) {
            Log::info('Change Password Notification Error : ' . $e->getMessage());
        }

        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }

        $data['is_logout'] = true;
        $data['message'] = __('message.changePasswordSuccess');

        return $data;
    }
}
