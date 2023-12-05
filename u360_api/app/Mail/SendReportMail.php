<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;


class SendReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailData;
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }


    public function build()
    {
        if ($this->mailData['put'] && $this->mailData['type'] == config('constants.esg_type')) {
            $path = storage_path() . config('constants.esg_path') . $this->mailData['file_name'];
            return $this->from(env('MAIL_FROM_ADDRESS'))
                ->subject($this->mailData['subject'])
                ->view('export_template.esg_report', ['mailData' => $this->mailData])
                ->attach($path);
        } elseif ($this->mailData['put'] && $this->mailData['type'] == config('constants.subscription_type')) {
            $path = storage_path() . config('constants.subscription_path') . $this->mailData['file_name'];
            return $this->from(env('MAIL_FROM_ADDRESS'))
                ->subject($this->mailData['subject'])
                ->view('export_template.subscription_report', ['mailData' => $this->mailData])
                ->attach($path);
        }

    }
}
