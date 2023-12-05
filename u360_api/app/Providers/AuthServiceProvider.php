<?php

namespace App\Providers;

use App\Models\Candidate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\User;
use App\Models\Employer;
use App\Models\AdminUser;
use App\Models\TemplateManagement;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $user = User::where('id', $notifiable->id)->first();
            $name = $user->name;

            $user->email_token = (sha1($notifiable->getEmailForVerification()));
            $user->save();
            $frontUrl = env('FRONT_URL');
            $url =  $frontUrl . "verify-email/" . $user->email_token;

            $emailTemplate = TemplateManagement::where('slug', 'verify_email')->first();
            $message = '';
            if (!empty($emailTemplate)) {
                $message = str_replace('##URL##', $url, $emailTemplate->template);
                $message = str_replace('##NAME##', $name, $message);
            }
            $details = [
                'subject' => 'Verify Email Address',
                'message' => $message,
            ];

            return (new MailMessage)
                ->markdown('emails.mail', ['mailData' => $details]);
        });

        ResetPassword::toMailUsing(function ($user, string $token) {
            if ($user != null) {
                $user->reset_password_token = $token;
                $user->save();
            }
            $url = '';
            $name = '';
            if ($user->getTable() && $user->getTable() === 'users') {
                $registeredUser = User::where(['email' => $user->email])->first();
                $url = env('FRONT_URL');
            } else {
                $url = env('ADMIN_URL');
                $registeredUser = AdminUser::where(['email' => $user->email])->first();
            }
            $url = $url . "reset-password/" . $token;
            if (!isset($registeredUser->name)) {
                $name = $registeredUser->first_name . ' ' . $registeredUser->last_name;
            } else {
                $name = $registeredUser->name;
            }
            $emailTemplate = TemplateManagement::where('slug', 'reset_password')->first();
            $message = '';
            if (!empty($emailTemplate)) {
                $current = ["##URL##", "##NAME##"];
                $new   = [$url, $name];
                $message = str_replace($current, $new, $emailTemplate->template);
            }
            $details = [
                'subject' => 'Reset Password',
                'message' => $message,
            ];

            return (new MailMessage)
                ->markdown('emails.mail', ['mailData' => $details]);
        });
    }
}
