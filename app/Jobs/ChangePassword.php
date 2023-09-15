<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Services\NotificationService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\ChangePassword as NotificationsChangePassword;

class ChangePassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $notificationService;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->notificationService = new NotificationService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'id' => null, //
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'image' => $this->user->image,
            'sub_title' => null,
            'type' => config('site.notifications.type.change_password'),
        ];

        $this->notificationService->store([
            'id' => Str::uuid()->toString(),
            'type' => config('site.notifications.type.change_password'),
            'title' => __('notification.change_password'),
            'description' => __('notification.change_password'),
            'notifiable_type' => 'User',
            'notifiable_id' => $this->user->id,
            'data' => json_encode($data),
            'notified_by' => $this->user->id,
        ]);

        $this->user->notify(new NotificationsChangePassword($this->user));
    }
}
