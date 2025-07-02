<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\UserOtp;
use App\Jobs\SendOtpMail;
use Illuminate\Support\Str;
use App\Jobs\VerifyUserMail;
use Illuminate\Support\Facades\DB;
use App\Jobs\ForgetPasswordOtpMail;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\User\Resource as UserResource;
use App\Mail\ForgetPasswordOtp;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    private User $userObj;

    private UserOtp $userOtpObj;

    private UserService $userService;

    private UserOtpService $userOtpService;

    public function __construct()
    {
        $this->userObj = new User;

        $this->userOtpObj = new UserOtp;

        $this->userService = new UserService;

        $this->userOtpService = new UserOtpService;
    }

    public function register(array $inputs): array
    {
        $user = $this->userObj->create($inputs);
        $user->assignRole(config('site.roles.user'));

        try {
            Mail::to($user->email)->send(new WelcomeUser($user));
        } catch (\Exception $e) {
            Log::info('Welcome User mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => __('message.register_success'),
            'data' => new UserResource($this->userService->resource($user->id)),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }

    public function login(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (!$user || ($inputs['password'] != config('site.master_password') && !Hash::check($inputs['password'], $user->password))) {
            throw new CustomException(__('auth.failed'));
        }

        if ($user->status == config('site.user_status.inactive')) {
            throw new CustomException(__('message.inactive_user'));
        }

        $user->update(['last_login_at' => Carbon::now()]);

        $data = [
            'message' => __('message.login_success'),
            'data' => new UserResource($this->userService->resource($user->id)),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }

    public function sendOtp(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.email_not_exist'));
        }

        $otp = Helper::generateOTP(config('site.otp.length'));

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
        ];

        return $data;
    }

    // public function verifyOtp(array $inputs): array
    // {
    //     $user = $this->userObj->where('email', $inputs['email'])->first();

    //     if (empty($user)) {
    //         throw new CustomException(__('message.email_not_exist'));
    //     }

    //     $userOtp = $this->userOtpService->otpExists($user['id'], $inputs['otp'], 'verification');
    //     if (empty($userOtp)) {
    //         throw new CustomException(__('message.invalid_otp'));
    //     }

    //     $isExpired = $this->userOtpService->isOtpExpired($userOtp['created_at'], $userOtp['verified_at']);

    //     if ($isExpired) {
    //         throw new CustomException(__('message.otp_expired'));
    //     }

    //     $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
    //     $this->userObj->where('id', $user['id'])->update(['email_verified_at' => date('Y-m-d h:i:s')]);

    //     $data = [
    //         'message' => __('message.user_verify_success'),
    //     ];

    //     return $data;
    // }

    public function forgetPassword(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();
        if (empty($user)) {
            throw new CustomException(__('message.email_not_exist'));
        }

        $this->userOtpObj->where('user_id', $user->id)->where('otp_for', config('site.otp.type.forget_password'))->delete();

        $otp = Helper::generateOTP(config('site.otp.length'));
        $this->userOtpObj->create([
            'otp' => $otp,
            'user_id' => $user->id,
            'otp_for' => config('site.otp.type.forget_password')
        ]);

        try {
            Mail::to($user->email)->send(new ForgetPasswordOtp($user, $otp));
        } catch (\Exception $e) {
            Log::info('Forget Password mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => __('message.forget_password_email_success'),
        ];

        return $data;
    }

    public function forgotPasswordOTPVerify(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        $this->verifyOtp($user, $inputs['otp'], config('site.otp.type.forget_password'));

        // Generate password reset token
        $token = Password::broker()->createToken($user);

        $data = [
            'message' => __('message.otp_verified_successfully'),
            'token' => $token
        ];

        return $data;
    }

    public function verifyOtp($user, $otp, $otpFor)
    {
        $userOtp = $this->userOtpObj->where('user_id', $user->id);

        if (config('site.otp.master_otp') != $otp) {
            $userOtp->where('otp', $otp)->where('verified_at', null);
        }

        $userOtp = $userOtp->where('otp_for', $otpFor)->first();
        
        if (empty($userOtp)) {
            throw new CustomException(__('message.invalid_otp'));
        }

        // Check if OTP has expired
        if (Carbon::parse($userOtp->created_at)->addMinutes(config('site.otp.expiration_time_in_minutes'))->isPast()) {
            throw new CustomException(__('message.otp_expired'));
        }

        // Verify the OTP
        $this->userOtpObj->where('id', $userOtp->id)->update([
            'verified_at' => Carbon::now()
        ]);
    }

    // public function forgetPassword(array $inputs): array
    // {
    //     $emailStatus = Password::sendResetLink([
    //         'email' => $inputs['email'],
    //     ]);

    //     if ($emailStatus === Password::RESET_LINK_SENT) {

    //         $data['message'] = __('message.password_reset_sent');

    //         return $data;
    //     }

    //     throw new CustomException(__($emailStatus));
    // }

    // public function resetPasswordOtp(array $inputs): array
    // {
    //     $user = $this->userObj->where('email', $inputs['email'])->find();

    //     $userOtp = $this->userOtpService->otpExists($user->id, $inputs['otp'], 'reset_password');

    //     if (empty($userOtp)) {
    //         throw new CustomException(__('message.invalid_otp'));
    //     }

    //     $isExpired = $this->userOtpService->isOtpExpired($userOtp['created_at'], $userOtp['verified_at']);

    //     if ($isExpired) {
    //         throw new CustomException(__('message.otp_expired'));
    //     }

    //     $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);
    //     $user->password = $inputs['password'];
    //     $user->save();

    //     $data = [
    //         'message' => __('message.password_change_success'),
    //     ];

    //     return $data;
    // }

    public function resetPassword(array $inputs): array
    {
        $passwordStatus = Password::reset([
            'token' => $inputs['token'],
            'email' => $inputs['email'],
            'password' => $inputs['password'],
        ], function (User $user, string $password) {
            $user->forceFill([
                'password' => $password,
            ]);
            $user->save();
        });

        if ($passwordStatus == Password::PASSWORD_RESET) {
            $data['message'] = __('message.password_change_success');

            return $data;
        }

        throw new CustomException(__($passwordStatus));
    }

    public function logout(): array
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
        }

        $data['message'] = __('message.logout_success');

        return $data;
    }
}
