<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $mail_subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {

        $this->data = $data;
        $this->mail_subject = $data['subject'];

        $this->message = (new MailMessage)
            ->greeting('Hello!')
            ->subject('My mail title')
            ->line('You have got 1 message from contact page!')
            ->line('From: '.$data['email'])
            ->line('Subject: '.$data['subject'])
            ->line('Person: '.$data['first_name'].' '.$data['last_name'])
            ->line('Message: '.$data['message']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('vendor.notifications.email', $this->message->data());
    }
}
