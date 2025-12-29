<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class AccountStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $linkUrl;
    public $locale;
    public $type;
    public $url;
    /**
     * Create a new message instance.
     */
    public function __construct(string $linkUrl, string $type = 'register', string $locale = 'en')
    {
        $this->linkUrl = $linkUrl;
        $this->locale = $locale;
        $this->type = $type;

        if ($type === 'not_exists') {
            $this->url = route('register');
        } else {
            $this->url = route('login');
        }
    }

    public function build()
    {
        App::setLocale($this->locale);
        $subjectKey = $this->type === 'not_exists'
            ? 'messages.email.account_not_exists.subject'
            : 'messages.email.account_exists.subject';

        return $this
            ->subject(__($subjectKey))
            ->view('emails.accountStatusMail');
    }
}
