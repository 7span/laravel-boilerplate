<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use App\Models\Notification;
use App\Traits\PaginationTrait;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

class NotificationService
{
    use PaginationTrait;

    private Notification $notificationObj;

    private UserDevice $userDeviceObj;

    public function __construct()
    {
        $this->notificationObj = new Notification;
        $this->userDeviceObj = new UserDevice;
    }

    /**
     * Fetch the notifications through the query builder so `filter`, `include`, `sort` and `fields` apply.
     *
     * @return LengthAwarePaginator<int, Notification>|Collection<int, Notification>
     */
    public function collection(User $user): LengthAwarePaginator|Collection
    {
        $notifications = $this->notificationObj->getQB()
            ->where('user_id', $user->id);

        return $this->paginationAttribute($notifications);
    }

    /**
     * Mark every unread notification of the user as read, or only the given ids.
     *
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function markAsRead(User $user, array $inputs): array
    {
        $this->notificationObj
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->when(! empty($inputs['ids']), fn ($query) => $query->whereIn('id', $inputs['ids']))
            ->update(['read_at' => now()]);

        return ['message' => __('message.notification_read_success')];
    }

    /**
     * Mark every read notification of the user as unread, or only the given ids.
     *
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function markAsUnread(User $user, array $inputs): array
    {
        $this->notificationObj
            ->where('user_id', $user->id)
            ->whereNotNull('read_at')
            ->when(! empty($inputs['ids']), fn ($query) => $query->whereIn('id', $inputs['ids']))
            ->update(['read_at' => null]);

        return ['message' => __('message.notification_unread_success')];
    }

    /**
     * Register, or refresh, the OneSignal player id of the device making the request.
     *
     * @param  array<string, mixed>  $inputs
     * @return array{message: string, data: UserDeviceResource}
     */
    public function setOnesignalData(User $user, array $inputs): array
    {
        $device = $this->userDeviceObj->updateOrCreate(
            ['onesignal_player_id' => $inputs['onesignal_player_id']],
            [
                'user_id' => $user->id,
                'device_id' => $inputs['device_id'] ?? null,
                'device_type' => $inputs['device_type'] ?? null,
            ],
        );

        return [
            'message' => __('message.onesignal_data_success'),
            'data' => new UserDeviceResource($device),
        ];
    }

    /**
     * @return array{unread_count: int}
     */
    public function unreadCount(User $user): array
    {
        $count = $this->notificationObj
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return ['unread_count' => $count];
    }
}
