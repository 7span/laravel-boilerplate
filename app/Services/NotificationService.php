<?php

namespace App\Services;

use App\Models\User;
use App\Traits\BaseModel;
use App\Models\Notification;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    use BaseModel, PaginationTrait;

    private $notificationObj;

    private $userObj;

    public function __construct()
    {
        $this->notificationObj = new Notification;
        $this->userObj = new User;
    }

    public function collection(array $inputs)
    {
        $notifications = $this->notificationObj->getQB()->where('notifiable_id', auth()->id());

        return (isset($inputs['limit']) && $inputs['limit'] == '-1') ? $notifications->get() : $notifications->paginate($inputs['limit']);
    }

    public function store(array $inputs)
    {
        $notification = $this->notificationObj->create($inputs);
        $data['message'] = __('entity.entityCreated', ['entity' => 'Notification']);
        $data['notification'] = $notification;

        return $data;
    }

    public function resource($id)
    {
        $notification = $this->notificationObj->getQB()->findOrFail($id);

        return $notification;
    }

    public function readAll()
    {
        $this->notificationObj->getQB()->where('notifiable_id', Auth::user()->id)->update(['read_at' => now()]);
        $data['message'] = __('entity.entityUpdated', ['entity' => 'Notification']);
        $this->userObj->getQB()->where('id', Auth::user()->id)->update(['un_read_notification_cnt' => 0]);

        return $data;
    }
}
