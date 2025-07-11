<?php

namespace App\Services;

use App\Models\UserDevice;
use App\Models\Notification;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

class NotificationService
{
    use PaginationTrait;

    private $notificationObj;

    public function __construct()
    {
        $this->notificationObj = new Notification();
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
            ->when(!empty($inputs['ids']), fn($q) => $q->whereIn('id', $inputs['ids']));

        $notifications->update(['read_at' => now()]);

        $data['message'] = __('message.notification_read_success');
        return $data;
    }

    public function setOnesignalData(array $data)
    {
        $device = UserDevice::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'onesignal_player_id' => $data['onesignal_player_id'],
                'device_id' => $data['device_id'] ?? null,
                'device_type' => $data['device_type'] ?? null,
            ]
        );

        $data['message'] = __('message.onesignal_data_success');
        $data['data'] = new UserDeviceResource($device->refresh());
        return $data;
    }
}
