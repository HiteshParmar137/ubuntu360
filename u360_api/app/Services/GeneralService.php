<?php

namespace App\Services;

use App\Models\User;
use App\Models\AdminUser;
use App\Models\TemplateManagement;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GeneralService
{
    public function sendForgotPasswordMail($user, $token, $successMessage)
    {
        try {
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
            $email = $user->email;
            Mail::send('emails.mail', ['mailData' => $details], function ($message) use ($email, $details) {
                $message->to(convertStringToLowercase($email));
                $message->subject($details['subject']);
            });
            return response()->json([
                'status_code' => 200, 'success' => true,
                'message' => $successMessage, 'data' => null
            ], 200);
        } catch (\Exception $e) {
            Log::info([
                'Error' => $e->getMessage(),
                'Line' => $e->getLine()
            ]);
            return response()->json([
                'status_code' => 500, 'success' => false,
                'message' => config('message.common_message.exception_error'), 'data' => null
            ], 500);
        }
    }
}
