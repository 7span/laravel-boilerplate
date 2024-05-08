<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\UserOtp;
use App\Jobs\VerifyUserMail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(private User $userObj, private UserOtp $userOtpObj, private UserOtpService $userOtpService)
    {
        //
    }

    public function resource(int $id, array $inputs = []): User
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    public function update(int $id, array $inputs = []): array
    {
        $user = User::find(auth()->id());

        if (!empty($inputs['email']) && $inputs['email'] != $user->email) {
            $this->userObj->whereId($user->id)->update(['email_verified_at' => null]);

            $otp = Helper::generateOTP(config('site.generateOtpLength'));

            $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

            try {
                VerifyUserMail::dispatch($user, $otp);
            } catch (\Exception $e) {
                Log::info('Verify user mail failed.' . $e->getMessage());
            }

            $data = $this->resource($id);
            $data->update($inputs);
            $data = [
                'message' => __('message.updateUserVerifySuccess'),
                'data' => $user->refresh(),
            ];
        } else {
            $data = $this->resource($id);
            $data->update($inputs);

            $data = [
                'message' => __('message.userProfileUpdate'),
                'data' => $user->refresh(),
            ];
        }

        return $data;
    }
}
