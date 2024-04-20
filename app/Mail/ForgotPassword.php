<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $userName;
    public $url;
    public function __construct($userName, $url)
    {
        $this->userName = $userName;
        $this->url = $url;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->url);
        return $this->view('email.forgot-password');
    }
}
