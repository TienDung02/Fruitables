<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class CompleteActionMail extends Mailable
{
    use Queueable, SerializesModels;
    use SerializesModels;

    public $token;
    public $type;
    public $locale;
    public $url;
    /**
     * Create a new message instance.
     */
    public function __construct(string $token, string $type = 'register', string $locale = 'en')
    {
        $this->token = $token;
        $this->type = $type;
        $this->locale = $locale;

        // Tạo URL với token
        if ($type === 'password_reset') {
            $this->url = route('password.retrieve', ['token' => $token]);
        } else {
            $this->url = route('register.username', ['token' => $token]);
        }
    }
    public function build()
    {
        App::setLocale($this->locale);

        $subjectKey = $this->type === 'password_reset'
            ? 'messages.email.complete_action.password_reset.subject'
            : 'messages.email.complete_action.register.subject';

        return $this
            ->subject(__($subjectKey))
            ->view('emails.completeActionMail');
    }
}
