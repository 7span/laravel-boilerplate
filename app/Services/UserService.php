<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Jobs\VerifyUserMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    private $userObj;

    private $userOtpService;

    public function __construct()
    {
        $this->userObj = new User();
        $this->userOtpService = new UserOtpService();
    }

    public function resource(int $id, array $inputs = []): User
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    public function update(int $id, array $inputs = []): array
    {
        $user = Auth::user();

        if (! empty($inputs['email']) && $inputs['email'] != $user->email) {
            $inputs['email_verified_at'] = null;
            $otp = Helper::generateOTP(config('site.generateOtpLength'));
            $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

            try {
                VerifyUserMail::dispatch($user, $otp);
            } catch (\Exception $e) {
                Log::info('User verification mail failed.' . $e->getMessage());
            }

            $user->update($inputs);
            $data = [
                'message' => __('message.updateUserVerifySuccess'),
                'data' => $user->refresh(),
            ];
        } else {
            $user->update($inputs);
            $data = [
                'message' => __('message.userProfileUpdate'),
                'data' => $user->refresh(),
            ];
        }

        return $data;
    }

    public function changeStatus(object $user, array $inputs = [])
    {
        $user->update($inputs);
        $data = [
            'message' => __('entity.entityUpdated', ['entity' => 'User status']),
        ];

        return $data;
    }
}
