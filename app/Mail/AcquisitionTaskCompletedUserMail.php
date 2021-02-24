<?php

namespace App\Mail;

use App\Modules\Auth\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AcquisitionTaskCompletedUserMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;

    /**
     * AcquisitionTaskCompletedUserMail constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.acquisition-task-completed-user-mail', [
            'user' => $this->user
        ]);
    }
}
