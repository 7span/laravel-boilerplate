<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserOtp;
use App\Helpers\Helper;
use App\Enums\OtpPurpose;
use App\Jobs\SendOtpMail;
use App\Jobs\VerifyUserMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\User\Resource as UserResource;
use App\Exceptions\CustomException;
use App\Jobs\ForgetPasswordOtpMail;

class AuthService
{
    public function __construct(User $user, UserOtp $userOtp, UserOtpService $userOtpService)
    {
        $this->userModel = $user;
        $this->userOtpModel = $userOtp;
        $this->userOtpService = $userOtpService;
    }

    public function signup(array $input): array
    {
        $user = $this->userModel->create($input);
        $otp = Helper::generateOTP(config('site.generate_otp_length'));

        $this->userOtpService->store([
            'otp' => $otp,
            'user_id' => $user->id,
            'otp_for' => OtpPurpose::VERIFICATION->value,
        ]);

        try {
            VerifyUserMail::dispatch($user, $otp);
        } catch (\Exception $e) {
            report($e);
        }

        return [
            'message' => __('message.userSignUpSuccess'),
            'data' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];
    }

    public function sendOtp(array $input): array
    {
        $user = $this->userModel->where('email', $input['email'])->firstOrFail();
        $otp = Helper::generateOTP(config('site.generate_otp_length'));

        $subjectMap = [
            OtpPurpose::VERIFICATION->value => __('email.verifyUserSubject'),
            OtpPurpose::UPDATE_PROFILE->value => __('email.updateProfileSubject'),
            OtpPurpose::RESET_PASSWORD->value => __('email.forgetPasswordEmailSubject'),
        ];
        $subject = $subjectMap[$input['otp_for']] ?? '';

        $this->userOtpService->store([
            'otp' => $otp,
            'user_id' => $user->id,
            'otp_for' => $input['otp_for'],
        ]);

        try {
            SendOtpMail::dispatch($user, $otp, $subject);
        } catch (\Exception $e) {
            report($e);
        }

        return ['message' => __('message.otpSentSuccessfully')];
    }

    public function verifyOtp(array $input): array
    {
        $user = $this->userModel->where('email', $input['email'])->firstOrFail();
        $userOtp = $this->userOtpService->otpExists($user->id, $input['otp'], OtpPurpose::VERIFICATION->value);

        if (!$userOtp || $this->userOtpService->isOtpExpired($userOtp->created_at, $userOtp->verified_at)) {
            throw new CustomException(__('message.invalidOrExpiredOtp'));
        }

        $this->userOtpService->update($userOtp->id, ['verified_at' => Carbon::now()]);
        $user->update(['email_verified_at' => Carbon::now()]);

        return ['message' => __('message.userVerifySuccess')];
    }

    public function login(array $input): array
    {
        $user = $this->userModel->where('email', $input['email'])->first();

        if (!$user || !Hash::check($input['password'], $user->password)) {
            throw new CustomException(__('auth.failed'));
        }

        if ($user->status === config('site.user_status.inactive')) {
            throw new CustomException(__('message.inactiveUser'));
        }

        $user->update(['last_login_at' => Carbon::now()]);

        return [
            'message' => __('message.loginSuccess'),
            'user' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];
    }

    public function forgetPasswordOtp(array $input): array
    {
        $user = $this->userModel->where('email', $input['email'])->firstOrFail();
        $this->userOtpModel->where('user_id', $user->id)->where('otp_for', OtpPurpose::RESET_PASSWORD->value)->delete();

        $otp = Helper::generateOTP(config('site.generate_otp_length'));
        $this->userOtpService->store([
            'otp' => $otp,
            'user_id' => $user->id,
            'otp_for' => OtpPurpose::RESET_PASSWORD->value,
        ]);

        try {
            ForgetPasswordOtpMail::dispatch($user, $otp);
        } catch (\Exception $e) {
            report($e);
        }

        return ['message' => __('message.forgetPasswordEmailSuccess')];
    }

    public function resetPasswordOtp(array $input): array
    {
        $user = $this->userModel->where('email', $input['email'])->firstOrFail();
        $userOtp = $this->userOtpService->otpExists($user->id, $input['otp'], OtpPurpose::RESET_PASSWORD->value);

        if (!$userOtp || $this->userOtpService->isOtpExpired($userOtp->created_at, $userOtp->verified_at)) {
            throw new CustomException(__('message.invalidOrExpiredOtp'));
        }

        $this->userOtpService->update($userOtp->id, ['verified_at' => Carbon::now()]);
        $user->update(['password' => Hash::make($input['password'])]);

        return ['message' => __('message.passwordChangeSuccess')];
    }

    public function resetPassword(array $input): array
    {
        $status = Password::reset([
            'token' => $input['token'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ], function (User $user, string $password) {
            $user->update(['password' => $password]);
        });

        if ($status === Password::PASSWORD_RESET) {
            return ['message' => __('message.passwordChangeSuccess')];
        }

        throw new CustomException(__($status));
    }

    public function changePassword(array $input): array
    {
        $user = Auth::user();

        if (!Hash::check($input['current_password'], $user->password)) {
            throw new CustomException(__('message.wrongCurrentPassword'));
        }

        if ($input['current_password'] === $input['password']) {
            throw new CustomException(__('message.newPasswordMatchedWithCurrentPassword'));
        }

        $user->update(['password' => Hash::make($input['password'])]);

        return ['message' => __('message.passwordChangeSuccess')];
    }

    public function logout(): array
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
        }

        return ['message' => __('message.logoutSuccess')];
    }
}
