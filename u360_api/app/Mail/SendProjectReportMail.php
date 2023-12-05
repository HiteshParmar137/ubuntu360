<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendProjectReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }


    public function build()
    {
        if ($this->mailData['put'] && $this->mailData['type'] == config('constants.project_type')) {
            $path = storage_path() . config('constants.project_report_path') . $this->mailData['file_name'];

            return $this->from(env('MAIL_FROM_ADDRESS'))
                ->subject($this->mailData['subject'])
                ->view('export_template.project_report', ['mailData' => $this->mailData])
                ->attach($path);
        } elseif ($this->mailData['put'] && $this->mailData['type'] == config('constants.user_type')) {
            $path = storage_path() . config('constants.user_path') . $this->mailData['file_name'];
            return $this->from(env('MAIL_FROM_ADDRESS'))
                ->subject($this->mailData['subject'])
                ->view('export_template.user_report', ['mailData' => $this->mailData])
                ->attach($path);
        }
    }
}
