<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — ' . config('app.name'))
            ->greeting('Bonjour ' . $notifiable->nom . ',')
            ->line('Vous recevez cet email parce qu\'une demande de réinitialisation de mot de passe a été effectuée pour votre compte.')
            ->action('Réinitialiser mon mot de passe', $url)
            ->line('Ce lien de réinitialisation expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé cette réinitialisation, aucune action supplémentaire n\'est requise.')
            ->salutation('Cordialement, l\'équipe ' . config('app.name'));
    }
}
