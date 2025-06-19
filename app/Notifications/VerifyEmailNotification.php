<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

return (new \Illuminate\Notifications\Messages\MailMessage)
    ->subject('Welcome to HireHub! Confirm Your Email')
    ->greeting('Hello!')
    ->line('Thank you for registering with HireHub.')
    ->line('Please click the button below to confirm your email address and activate your account.')
    ->action('Confirm Email', $verificationUrl)
    ->line('If you did not register, no further action is needed.')
    ->salutation('Best regards, The HireHub Team');

    }

protected function verificationUrl($notifiable)
{
    $frontendUrl = config('app.frontend_url', 'http://localhost:3000');

    $temporarySignedUrl = URL::temporarySignedRoute(
        'verification.verify',
        Carbon::now()->addMinutes(15),
        [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]
    );

    // Turn backend URL into frontend-friendly format
    $parsed = parse_url($temporarySignedUrl);
    parse_str($parsed['query'], $query); // extract query params

    // This will generate something like:
    // http://localhost:3000/verify-email?id=1&hash=abc123&expires=...&signature=...
    return $frontendUrl . '/verify-email?' . http_build_query([
        'id' => $notifiable->getKey(),
        'hash' => sha1($notifiable->getEmailForVerification()),
        'expires' => $query['expires'],
        'signature' => $query['signature'],
    ]);
}
}
