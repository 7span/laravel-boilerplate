<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Jobs\VerifyUserMail;
use App\Library\MediaHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function __construct(private User $userObj, private UserOtpService $userOtpService)
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

        if (isset($inputs['profile_image'])) {

            $mediaTag = config('site.media_tags.profile_image');

            $mediaIds = MediaHelper::attachMedia($inputs['profile_image'], $mediaTag, $id, User::class, 'profile_image');

            // if (!empty($inputs['profile_image'])) {
            //     Storage::disk('public')->delete($inputs['profile_image']);
            // }

            // $data['profile_image'] = $inputs['profile_image']->store('/profile_image', 'profile_image');
        }

        return $data;
    }
}
