<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\UserOtp;
use App\Jobs\SendOtpMail;
use App\Jobs\VerifyUserMail;
use App\Jobs\ForgetPasswordMail;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\Resource as UserResource;

class AuthService
{
    public function __construct(private User $userObj, private UserOtp $userOtpObj, private UserOtpService $userOtpService)
    {
        //
    }

    public function signup($inputs)
    {
        $user = $this->userObj->create($inputs);
        $otp = Helper::generateOTP(config('site.generateOtpLength'));
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

        try {
            VerifyUserMail::dispatch($user, $otp);
        } catch (\Exception $e) {
            Log::info('User verification mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => __('message.userSignUpSuccess'),
            'data' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }

    public function sendOtp($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $otp = Helper::generateOTP(config('site.generateOtpLength'));

        switch ($inputs['otp_for']) {
            case 'verification':
                $subject = __('email.verifyUserSubject');
                break;
            case 'update_profile':
                $subject = __('email.updateProfileSubject');
                break;
            case 'reset_password':
                $subject = __('email.forgetPasswordEmailSubject');
                break;
            default:
                $subject = '';
                break;
        }

        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user['id'], 'otp_for' => $inputs['otp_for']]);

        try {
            SendOtpMail::dispatch($user, $otp, $subject);
        } catch (\Exception $e) {
            Log::info('Send Otp mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => 'Otp Send Successfully',
            'data' => ['otp' => $otp],
        ];

        return $data;
    }

    public function verifyOtp($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $userOtp = $this->userOtpService->otpExists($user['id'], $inputs['otp'], 'verification');
        if (empty($userOtp)) {
            throw new CustomException(__('message.invalidOtp'));
        }

        $isExpired = $this->userOtpService->isOtpExpired($userOtp['created_at'], $userOtp['verified_at']);

        if ($isExpired) {
            throw new CustomException(__('message.otpExpired'));
        }

        $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
        $this->userObj->whereId($user['id'])->update(['email_verified_at' => date('Y-m-d h:i:s')]);

        $data = [
            'message' => __('message.userVerifySuccess'),
        ];

        return $data;
    }

    public function login($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (! $user || ! Hash::check($inputs['password'], $user->password)) {
            throw new CustomException(__('auth.failed'));
        }

        $data = [
            'message' => 'Login successfully',
            'user' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }

    public function forgetPassword($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();
        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $this->userOtpObj->whereUserId($user['id'])->where('otp_for', 'reset_password')->delete();

        $otp = Helper::generateOTP(config('site.generateOtpLength'));
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'reset_password']);

        try {
            ForgetPasswordMail::dispatch($user, $otp);
        } catch (\Exception $e) {
            Log::info('Forget Password mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => __('message.forgetPasswordEmailSuccess'),
        ];

        return $data;
    }

    public function resetPassword($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.emailNotExist'));
        }

        $userOtp = $this->userOtpService->otpExists($user['id'], $inputs['otp'], 'reset_password');

        if (empty($userOtp)) {
            throw new CustomException(__('message.invalidOtp'));
        }

        $isExpired = $this->userOtpService->isOtpExpired($userOtp['created_at'], $userOtp['verified_at']);

        if ($isExpired) {
            throw new CustomException(__('message.otpExpired'));
        }

        $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
        $user->password = $inputs['password'];
        $user->save();

        $data = [
            'message' => __('message.passwordChangeSuccess'),
        ];

        return $data;
    }

    public function changePassword($inputs)
    {
        $user = Auth::user();
        $currentPassword = trim($inputs['current_password']);
        $newPassword = trim($inputs['password']);

        if (strcmp($currentPassword, $newPassword) == 0) {
            throw new CustomException(__('message.newPasswordMatchedWithCurrentPassword'));
        }

        if (! Hash::check($inputs['current_password'], $user->password)) {
            throw new CustomException(__('message.wrongCurrentPassword'));
        }

        $user->password = $newPassword;
        $user->save();

        $data = [
            'message' => __('message.passwordChangeSuccess'),
        ];

        return $data;
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
        }
        $data = [
            'message' => __('message.logoutSuccess'),
        ];

        return $data;
    }
}
