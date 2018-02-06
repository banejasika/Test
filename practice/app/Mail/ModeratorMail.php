<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ModeratorMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailData;
    protected $moderator;

    /**
     * Create a new message instance.
     *
     * @param $emailData
     *
     * @param $moderator
     *
     * @return void
     */
    public function __construct($emailData, $moderator)
    {
        $this->emailData = $emailData;
        $this->moderator = $moderator;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = Config('app.url');

        return $this->view('emails.moderator_mail')
            ->with([
                'title' => $this->emailData['title'],
                'description' => $this->emailData['description'],
                'url' => $url . '/email/approve?token=' . base64_decode($this->emailData['hash']) . '&uid=' . $this->moderator['id'],
            ]);
    }
}