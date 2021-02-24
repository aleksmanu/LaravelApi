<?php

namespace App\Notifications;

use App\Mail\AcquisitionTaskCompletedAdminMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyAdminAcquisitionTaskCompleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            //'slack',
            'mail',
        ];
    }

    /**
     * @param $notifiable
     * @return AcquisitionTaskCompletedAdminMail
     */
    public function toMail($notifiable)
    {
        return new AcquisitionTaskCompletedAdminMail($notifiable);
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->from("Aron's Evil Twin", "devil")
            ->to('developers')
            ->content("Stop bothering the important people with that bot crap!");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
