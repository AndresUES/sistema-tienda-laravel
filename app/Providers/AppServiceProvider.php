<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
  public function boot(): void
{
    ResetPassword::toMailUsing(function ($notifiable, $token) {
        return (new MailMessage)
            ->subject('Restablecer contraseña')
            ->greeting('¡Hola!')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->action(
                'Restablecer contraseña',
                url(config('app.url') . route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false))
            )
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si tú no solicitaste este cambio, puedes ignorar este correo.')
            ->salutation('Saludos,');
    });
}



}

