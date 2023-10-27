<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOtp;
use App\Helpers\Helper;
use App\Jobs\VerifyUserMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(private User $userObj, private UserOtp $userOtpObj, private UserOtpService $userOtpService)
    {
        //
    }

    public function resource($id, $inputs = null)
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    public function update($id, $inputs = null)
    {
        $user = Auth::user();

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
                'status' => true,
                'message' => __('message.updateUserVerifySuccess'),
            ];
        } else {
            $data = $this->resource($id);
            $data->update($inputs);

            $data = [
                'status' => true,
                'message' => __('message.userProfileUpdate')
            ];
        }

        return $data;
    }
}
