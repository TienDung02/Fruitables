<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountAlreadyExistsMail extends Mailable
{
    use Queueable, SerializesModels;
    use SerializesModels;

    public string $url;
    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $this->url = route('register');
    }
    public function build()
    {
        return $this
            ->subject('Hoàn tất đăng ký tài khoản')
            ->view('emails.AccountAlreadyExistsMail');
    }
}
