<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        $user = $user = $this->userObj->find(Auth::user()->id);

        if (! empty($inputs['email']) && $inputs['email'] != $user->email) {
            $inputs['email_verified_at'] = null;

            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                Log::info('User verification mail failed.' . $e->getMessage());
            }
        }

        $user->update($inputs);

        $data = [
            'message' => __('message.userProfileUpdate'),
        ];

        return $data;
    }

    public function changeStatus($inputs)
    {
        $user = $this->userObj->findOrFail($inputs['user_id']);
        $user->status = $inputs['status'];
        $user->save();

        $data = [
            'message' => __('message.changeStatusSuccess', ['status' => $inputs['status']]),
        ];

        return $data;
    }
}
