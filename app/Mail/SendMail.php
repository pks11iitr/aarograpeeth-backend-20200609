<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $view;
    public $subject;
    private $from_user;
    private $to_user;
    private $data;
    private $attach;
    private $reply_to;
    private $onqueue;


    /**
     * Create a new message instance.
     * @param object $object
     * @param string $email_view
     * @param string $from_user
     * @param string $email_subject
     * @param array $data
     * @param string $attach
     * @param array $reply_to
     * @return void
     */
    public function __construct($from_user, $to_user, $email_subject, $email_view, $data = [], $attach = null, $reply_to = [], $queue=null)
    {
        $this->subject = $email_subject;
        $this->from_user = $from_user;
        $this->to_user = $to_user;
        $this->view = $email_view;
        $this->data = $data;
        $this->attach = $attach;
        $this->reply_to = $reply_to;
        $this->onqueue=$queue;
    }

    /**
     * Build the message.
     *
     * @return \App\Mail\SendMail
     */
    public function build()
    {
        $mail = $this->view($this->view)
            ->with(['data' => $this->data])
            ->from($this->from_user)
            ->subject($this->subject)
            ->to($this->to_user);

        if (!empty($this->attach)) {
            if (is_array($this->attach)) {
                foreach ($this->attach as $attach) {
                    $mail = $mail->attach($attach);
                }
            } else {
                $mail = $mail->attach($this->attach);
            }
        }

        if (!empty($this->reply_to)){
            $mail = $mail->replyTo($this->reply_to);
        }

        if(!empty($this->onqueue))
            $mail=$mail->onQueue($this->onqueue);

        return $mail;
    }
}
