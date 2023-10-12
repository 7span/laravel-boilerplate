<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserOtp;
use App\Jobs\SendOtpMail;
use App\Jobs\VerifyUserMail;
use App\Jobs\ForgetPasswordMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\User\Resource as UserResource;

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
        $user = $this->userObj->create($inputs);
        $otp = mt_rand(100000, 999999);
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

        try {
            VerifyUserMail::dispatch($user, $otp);
        } catch (\Exception $e) {
            Log::info('User verification mail failed.' . $e->getMessage());
        }

        $data = [
            'status' => true,
            'message' => __('message.userSignUpSuccess'),
            'user' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }

    public function verifyOtp($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (empty($user)) {
            $data = [
                'status' => false,
                'message' =>  __('message.emailNotExist')
            ];
            return $data;
        }

        $userOtp = $this->userOtpObj->whereUserId($user['id'])->whereOtp($inputs['otp'])->where('otp_for', 'verification')->first();

        if (empty($userOtp)) {
            $data = [
                'status' => false,
                'message' =>  __('message.invalidOtp')
            ];
            return $data;
        }

        $expirationTime = config('site.otpExpirationTimeInMinutes');
        $expirationDate = Carbon::parse($userOtp['created_at'])->addMinutes($expirationTime)->format('Y-m-d H:i:s');

        if ($userOtp['verified_at'] != null || date('Y-m-d h:i:s') > $expirationDate) {
            $data = [
                'status' => false,
                'message' =>  __('message.invalidOtp')
            ];
            return $data;
        }

        $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
        $this->userObj->whereId($user['id'])->update(['email_verified_at' => date('Y-m-d h:i:s')]);

        $data = [
            'status' => true,
            'message' => __('message.userVerifySuccess')
        ];
        return $data;
    }

    public function login($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (!$user || !Hash::check($inputs['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $data = [
            'status' => true,
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
            $data['errors']['email'][] = __('message.emailNotExist');

            return $data;
        }

        $this->userOtpObj->whereUserId($user['id'])->where('otp_for', 'reset_password')->delete();

        $otp = mt_rand(100000, 999999);
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'reset_password']);

        try {
            ForgetPasswordMail::dispatch($user, $otp);
        } catch (\Exception $e) {
            Log::info('Forget Password mail failed.' . $e->getMessage());
        }

        $data = [
            'status' => true,
            'message' => __('message.forgetPasswordEmailSuccess')
        ];

        return $data;
    }

    public function resetPassword($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (empty($user)) {
            $data = [
                'status' => false,
                'message' =>  __('message.emailNotExist')
            ];
            return $data;
        }

        $userOtp = $this->userOtpObj->whereUserId($user['id'])->whereOtp($inputs['otp'])->where('otp_for', 'reset_password')->first();

        if (empty($userOtp)) {
            $data = [
                'status' => false,
                'message' => __('message.invalidOtp')
            ];

            return $data;
        }

        $expirationTime = config('site.otpExpirationTimeInMinutes');
        $expirationDate = Carbon::parse($userOtp['created_at'])->addMinutes($expirationTime)->format('Y-m-d H:i:s');

        if ($userOtp['verified_at'] != null || date('Y-m-d h:i:s') > $expirationDate) {
            $data = [
                'status' => false,
                'message' => __('message.invalidOtp')
            ];

            return $data;
        }

        $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
        $user->password = $inputs['password'];
        $user->save();

        $data = [
            'status' => true,
            'message' => __('message.passwordChangeSuccess')
        ];

        return $data;
    }

    public function sendOtp($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();

        if (empty($user)) {
            $data = [
                'status' => false,
                'message' =>  __('message.emailNotExist')
            ];
            return $data;
        }

        $otp = mt_rand(100000, 999999);

        switch ($inputs['otp_for']) {
            case 'verification':
                $subject = __('email.signUpEmailSubject');
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

        $data['otp'] = $otp;

        $data = [
            'status' => true,
            'message' => 'Otp Send Successfully'
        ];

        return $data;
    }

    public function changePassword($inputs)
    {
        $user = Auth::user();
        $currentPassword = trim($inputs['current_password']);
        $newPassword = trim($inputs['password']);

        if (strcmp($currentPassword, $newPassword) == 0) {
            $data['errors']['message'] = __('message.newPasswordMatchedWithCurrentPassword');

            return $data;
        }

        if (!Hash::check($inputs['current_password'], $user->password)) {
            $data['errors']['message'] = __('message.wrongCurrentPassword');
            return $data;
        }

        $user->password = $newPassword;
        $user->save();

        $data = [
            'status' => true,
            'message' =>  __('message.passwordChangeSuccess')
        ];

        return $data;
    }
}
