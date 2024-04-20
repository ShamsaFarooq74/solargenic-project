<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactNOC extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $userName;
    public $plantName;
    public $platform;
    public $title;
    public $description;

    public function __construct($userName,$plantName,$platform,$title,$description)
    {
        $this->userName = $userName;
        $this->plantName = $plantName;
        $this->platform = $platform;
        $this->title = $title;
        $this->description = $description;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bel App Message Received')->view('email.contact-noc');
    }
}
