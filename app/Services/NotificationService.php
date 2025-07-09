<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Notification;
use App\Models\UserDevice;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    use PaginationTrait;

    private Notification $notificationObj;

    public function __construct()
    {
        $this->notificationObj = new Notification;
    }

    public function collection()
    {
        $notifications = $this->notificationObj->getQB()
            ->where('user_id', Auth::id());

        return $this->paginationAttribute($notifications);
    }

    public function readAllNotification(array $inputs)
    {
        $notifications = $this->notificationObj
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->when(!empty($input['ids']), fn($q) => $q->whereIn('id', $inputs['ids']));

        $notifications->update(['read_at' => now()]);

        // $message = 
        $data['message'] = __('success');
        return $data;
    }

    public function setOnesignalData(array $data)
    {
        $device = UserDevice::updateOrCreate(
            ['user_id' => Auth::id()],
            ['onesignal_player_id' => $data['onesignal_player_id']]
        );

        return [
            'message' => __('entity.entityAdded', ['entity' => "Onesignal Player ID"]),
            'data' => $device
        ];
    }
}