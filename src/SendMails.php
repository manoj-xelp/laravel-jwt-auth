<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMails extends Mailable
{
    use Queueable, SerializesModels;

    public $mail_details;
    public $subject;
    public $view;
    public $cc_mail;
    public $bcc_mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_details,$subject,$view,$cc_mail,$bcc_mail)
    {
        $this->mail_details=$mail_details;
        $this->subject=$subject;
        $this->view=$view;
        $this->cc_mail=$cc_mail;
        $this->bcc_mail=$bcc_mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view)
            ->subject($this->subject)
            ->cc($this->cc_mail)
            ->bcc($this->bcc_mail)
            ->with($this->mail_details);
    }
}
