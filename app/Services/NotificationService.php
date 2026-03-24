<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserDevice;
use App\Models\Notification;
use App\Traits\PaginationTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

class NotificationService
{
    use PaginationTrait;

    private Notification $notificationObj;

    public function __construct()
    {
        $this->notificationObj = new Notification;
    }

    /**
     * @return LengthAwarePaginator<int, \Illuminate\Database\Eloquent\Model>|Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function collection(): LengthAwarePaginator|Collection
    {
        $notifications = $this->notificationObj->getQB()
            ->where('user_id', Auth::id());

        /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $notificationsModel @phpstan-ignore varTag.type */
        $notificationsModel = $notifications;
        return $this->paginationAttribute($notificationsModel);
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function readAllNotification(array $inputs): array
    {
        $notifications = $this->notificationObj
            ->where('user_id', Auth::id())
            ->whereNull('read_at')
            ->when(! empty($inputs['ids']), fn ($q) => $q->whereIn('id', $inputs['ids']));

        /** @var \Illuminate\Database\Eloquent\Builder<Notification> $notifications */
        $notifications->update(['read_at' => now()]);

        $data['message'] = __('message.notification_read_success');

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function setOnesignalData(array $data): array
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
