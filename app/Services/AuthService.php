<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Mail\SignUp;
use App\Models\User;
use App\Data\UserData;
use App\Models\UserOtp;
use App\Jobs\ForgetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        try {
            $user = $this->userObj->create($inputs);
            $otp = mt_rand(100000, 999999);
            $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);
            Mail::to($user->email)->send(new SignUp(['otp' => $otp, 'used_for' => 'SignUp']));
            $data = [
                'status' => true,
                'message' => 'Otp sent to your mail.Please Verify your account via mail.',
                'user' => UserData::from($user),
                'token' => $user->createToken(config('app.name'))->plainTextToken,
            ];
        } catch (Exception $e) {
            $data = [
                'status' => false,
                'message' => 'Something went wrong'
            ];
        }
        return $data;
    }

    public function verifyOtp($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();
        if ($user == null) {
            $data = [
                'status' => false,
                'message' =>  __('message.emailNotExist')
            ];
            return $data;
        }

        $userOtp = $this->userOtpObj->whereUserId($user['id'])->whereOtp($inputs['otp'])->where('otp_for', 'verification')->first();
        if ($userOtp == null) {
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
        $this->userObj->where('id', $user['id'])->update(['verified_at' => date('Y-m-d h:i:s')]);

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
        if (!$user['verified_at']) {
            $data = [
                'status' => false,
                'message' =>  __('message.userVerifyFailure')
            ];
            return $data;
        }
        $data = [
            'status' => true,
            'message' => 'Login successfully',
            'user' => UserData::from($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];
        return $data;
    }

    public function forgetPassword($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();
        if ($user == null) {
            $data['errors']['email'][] = __('message.emailNotExist');

            return $data;
        }
        $this->userOtpObj->whereUserId($user['id'])->where('otp_for', 'reset_password')->delete();

        $otp = mt_rand(100000, 999999);
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'reset_password']);

        ForgetPasswordMail::dispatch($user, $otp);

        $data = [
            'status' => true,
            'message' => __('message.forgetPasswordEmailSuccess')
        ];

        return $data;
    }

    public function resetPassword($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();
        if ($user == null) {
            $data = [
                'status' => false,
                'message' =>  __('message.emailNotExist')
            ];
            return $data;
        }

        $userOtp = $this->userOtpObj->whereUserId($user['id'])->whereOtp($inputs['otp'])->where('otp_for', 'reset_password')->first();
        if ($userOtp == null) {
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

    public function generateOtp($inputs)
    {
        $user = $this->userObj->whereEmail($inputs['email'])->first();
        if ($user == null) {
            $data = [
                'status' => false,
                'message' =>  __('message.emailNotExist')
            ];
            return $data;
        }

        $otp = mt_rand(100000, 999999);
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user['id'], 'otp_for' => $inputs['otp_for']]);
        Mail::to($inputs['email'])->send(new SignUp(['otp' => $otp, 'used_for' => 'Update profile']));
        $data['otp'] = $otp;

        $data = [
            'status' => true,
            'message' => 'Otp Send Successfully'
        ];

        return $data;
    }

    public function changePassword($inputs)
    {
        $currentPassword = $inputs['current_password'];
        $newPassword = $inputs['password'];
        $user = Auth::user();
        $user->password = $newPassword;
        $user->save();
        $data = [
            'status' => true,
            'message' =>  __('message.passwordChangeSuccess')
        ];
        return $data;
    }
}
