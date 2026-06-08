<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserOtp;
use App\Enums\UserOtpFor;
use App\Enums\UserStatus;
use App\Libraries\Helper;
use App\Mail\WelcomeUser;
use App\Models\UserDevice;
use App\Mail\ForgetPasswordOtp;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function register(array $inputs): array
    {
        $user = $this->userObj->create($inputs);
        /** @var string|int $role */
        $role = config('site.roles.user');
        $user->assignRole($role);

        try {
            Mail::to($user)->send(new WelcomeUser($user));
        } catch (\Exception $e) {
            Log::info('Welcome User mail failed.' . $e->getMessage());
        }

        $data = [
            'message' => __('message.register_success'),
            'data' => new UserResource($this->userService->resource($user->id)),
<<<<<<< HEAD
            'token' => $user->createToken(is_string($n = config('app.name')) ? $n : '')->plainTextToken,
=======
            'token' => $user->createToken(config('app.name'))->accessToken,
>>>>>>> origin/master
        ];

        return $data;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function login(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        /** @var string $password */
        $password = is_string($inputs['password'] ?? null) ? $inputs['password'] : '';

        if (! $user || ($password !== config('site.master_password') && ! Hash::check($password, $user->password))) {
            throw new CustomException(__('auth.failed'));
        }

        if ($user->status === UserStatus::INACTIVE) {
            throw new CustomException(__('message.inactive_user'));
        }

        $user->update(['last_login_at' => Carbon::now()]);
        $data = [
            'message' => __('message.login_success'),
            'data' => new UserResource($this->userService->resource($user->id)),
<<<<<<< HEAD
            'token' => $user->createToken(is_string($n = config('app.name')) ? $n : '')->plainTextToken,
=======
            'token' => $user->createToken(config('app.name'))->accessToken,
>>>>>>> origin/master
        ];

        return $data;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function forgotPassword(array $inputs): array
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (empty($user)) {
            throw new CustomException(__('message.email_not_exist'));
        }

        $this->userOtpObj->where('user_id', $user->id)
            ->where('otp_for', UserOtpFor::FORGOT_PASSWORD)
            ->delete();

        $val = config('site.otp.length', 6);
        $otpLength = is_scalar($val) ? (int) $val : 6;
        $otp = Helper::generateOTP($otpLength);
        $this->userOtpObj->create([
            'otp' => $otp,
            'user_id' => $user->id,
            'otp_for' => UserOtpFor::FORGOT_PASSWORD,
        ]);

        try {
<<<<<<< HEAD
            /** @var string $otpStr */
            $otpStr = (string) $otp;
            Mail::to($user->email)->send(new ForgetPasswordOtp($user, $otpStr));
=======
            Mail::to($user)->send(new ForgetPasswordOtp($user, $otp));
>>>>>>> origin/master
        } catch (\Exception $e) {
            Log::info('Forget Password mail failed.' . $e->getMessage());
        }

        $data['message'] = __('message.forget_password_email_success');

        return $data;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function forgotPasswordOTPVerify(array $inputs): array
    {
        $user = $this->userObj->firstWhere('email', $inputs['email']);
        if (!$user) {
            throw new CustomException(__('auth.failed'));
        }

        /** @var string $otp */
        $otp = $inputs['otp'];
        $this->verifyOtp($user, $otp, UserOtpFor::FORGOT_PASSWORD);

        // Generate password reset token
        $token = Password::broker()->createToken($user);

        $data = [
            'message' => __('message.otp_verified_successfully'),
            'token' => $token,
        ];

        return $data;
    }

    public function verifyOtp(User $user, string $otp, UserOtpFor $otpFor): void
    {
        $userOtp = $this->userOtpObj->where('user_id', $user->id);

        if (config('site.otp.master_otp') !== $otp) {
            $userOtp->where('otp', $otp)->whereNull('verified_at');
        }

        $userOtp = $userOtp->where('otp_for', $otpFor)->first();

        if (empty($userOtp)) {
            throw new CustomException(__('message.invalid_otp'));
        }

        // Check if OTP has expired
        /** @var int|float $expiration */
        $expiration = config('site.otp.expiration_time_in_minutes');
        /** @var string|null $createdAt */
        $createdAt = $userOtp->created_at;
        if ($createdAt && Carbon::parse($createdAt)->addMinutes($expiration)->isPast()) {
            throw new CustomException(__('message.otp_expired'));
        }

        // Verify the OTP
        $this->userOtpObj->where('id', $userOtp->id)->update([
            'verified_at' => Carbon::now(),
        ]);
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
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

        if ($passwordStatus === Password::PASSWORD_RESET) {
            $data['message'] = __('message.password_change_success');

            return $data;
        }

        $statusStr = is_string($passwordStatus) ? $passwordStatus : '';
        $message = __($statusStr);
        throw new CustomException((string) $message);
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function logout(array $inputs): array
    {
        if (isset($inputs['onesignal_player_id']) && $inputs['onesignal_player_id']) {
            UserDevice::where('onesignal_player_id', $inputs['onesignal_player_id'])->delete();
        }

        $token = Auth::user()->token();

        if ($token instanceof \Laravel\Passport\Token) {
            $token->revoke();
        }
        $data['message'] = __('message.logout_success');

        return $data;
    }
}
