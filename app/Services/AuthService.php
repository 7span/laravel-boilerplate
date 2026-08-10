<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOtp;
use App\Enums\UserOtpFor;
use App\Enums\UserStatus;
use App\Libraries\Helper;
use App\Notifications\WelcomeUser;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ForgotPasswordOtp;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\User\Resource as UserResource;

class AuthService
{
    private User $userObj;

    private UserOtp $userOtpObj;

    private UserService $userService;

    public function __construct()
    {
        $this->userObj = new User;
        $this->userOtpObj = new UserOtp;
        $this->userService = new UserService;
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string, data: UserResource, token: string}
     */
    public function register(array $inputs): array
    {
        $createdUser = $this->userObj->create($inputs);
        $createdUser->assignRole(config('site.roles.user'));

        $user = $this->userService->resource($createdUser->id);

        $user->notify(new WelcomeUser);

        return [
            'message' => __('message.register_success'),
            'data' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string, data: UserResource, token: string}
     */
    public function login(array $inputs): array
    {
        $user = $this->userObj->firstWhere('email', $inputs['email']);

        if (! $user instanceof User || ! $this->isValidPassword($inputs['password'], $user->password)) {
            throw new CustomException(__('message.invalid_credentials'), 401);
        }

        if ($user->status === UserStatus::INACTIVE) {
            throw new CustomException(__('message.inactive_user'), 403);
        }

        $user->update(['last_login_at' => now()]);

        $user = $this->userService->resource($user->id);

        return [
            'message' => __('message.login_success'),
            'data' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function forgotPassword(array $inputs): array
    {
        $user = $this->userObj->firstWhere('email', $inputs['email']);

        if (! $user instanceof User) {
            throw new CustomException(__('message.email_not_exist'), 404);
        }

        $this->userOtpObj->newQuery()
            ->where('user_id', $user->id)
            ->where('otp_for', UserOtpFor::FORGOT_PASSWORD)
            ->delete();

        $otp = Helper::generateOTP(config('site.otp.length'));

        $this->userOtpObj->create([
            'user_id' => $user->id,
            'otp' => $otp,
            'otp_for' => UserOtpFor::FORGOT_PASSWORD,
        ]);

        $user->notify(new ForgotPasswordOtp($otp));

        return [
            'message' => __('message.forgot_password_email_success'),
        ];
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string, token: string}
     */
    public function verifyForgotPasswordOtp(array $inputs): array
    {
        $user = $this->userObj->firstWhere('email', $inputs['email']);

        if (! $user instanceof User) {
            throw new CustomException(__('message.email_not_exist'), 404);
        }

        $this->verifyOtp($user, $inputs['otp'], UserOtpFor::FORGOT_PASSWORD);

        return [
            'message' => __('message.otp_verified_successfully'),
            'token' => Password::broker()->createToken($user),
        ];
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function resetPassword(array $inputs): array
    {
        $status = Password::reset([
            'token' => $inputs['token'],
            'email' => $inputs['email'],
            'password' => $inputs['password'],
        ], function (User $user, string $password): void {
            $user->forceFill(['password' => $password])->save();

            $user->tokens()->delete();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw new CustomException(__($status));
        }

        return [
            'message' => __('message.password_change_success'),
        ];
    }

    /**
     * @return array{message: string}
     */
    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();

        return [
            'message' => __('message.logout_success'),
        ];
    }

    /**
     * Verify an OTP for the given purpose and mark it as verified.
     */
    public function verifyOtp(User $user, string $otp, UserOtpFor $otpFor): void
    {
        $query = $this->userOtpObj->newQuery()
            ->where('user_id', $user->id)
            ->where('otp_for', $otpFor);

        $masterOtp = config('site.otp.master_otp');

        if (empty($masterOtp) || $masterOtp !== $otp) {
            $query->where('otp', $otp)->whereNull('verified_at');
        }

        $userOtp = $query->latest('id')->first();

        if (! $userOtp instanceof UserOtp) {
            throw new CustomException(__('message.invalid_otp'));
        }

        $expiresAt = Date::createFromTimestamp($userOtp->created_at)
            ->addMinutes(config('site.otp.expiration_time_in_minutes'));

        if ($expiresAt->isPast()) {
            throw new CustomException(__('message.otp_expired'));
        }

        $userOtp->update(['verified_at' => now()]);
    }

    /**
     * Allow the configured master password to bypass the hash check in non-production environments.
     */
    private function isValidPassword(string $password, string $hashedPassword): bool
    {
        $masterPassword = config('site.master_password');

        if (! empty($masterPassword) && $password === $masterPassword) {
            return true;
        }

        return Hash::check($password, $hashedPassword);
    }
}
