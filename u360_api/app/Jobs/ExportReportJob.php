<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendReportMail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Exports\EsgReportExport;
use App\Exports\SubscriptionExport;
use App\Models\TemplateManagement;

/**
 * Create a new job instance.
 *
 * @return void
 */

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    /* Global Variable intialiation */
    public $data = [];

    public function __construct(array $data)
    {
        $this->data   = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailData = [];
        if ($this->data['type'] == config('constants.esg_type')) {
            $mailData['put'] = Excel::store(new EsgReportExport, 'esg_report/' . $this->data['file_name']);

            $emailTemplate = TemplateManagement::select('template', 'subject')
                ->where('slug', 'esg_report')->first();

            $mailData = [
                'message' => $emailTemplate->template ?? '',
                'subject' => $emailTemplate->subject ?? '',
                'file_name' => $this->data['file_name'],
                'type' => config('constants.esg_type'),
                'put' => $mailData['put'],
            ];


            $check = Mail::to(config('constants.admin_mail'))->send(new SendReportMail($mailData));

            //email sent success
            if ($check instanceof \Illuminate\Mail\SentMessage) {
                $path = storage_path() . config('constants.esg_path') . $this->data['file_name'];
                unlink($path);
            }
        } elseif ($this->data['type'] == config('constants.subscription_type')) {
            $mailData['put'] = Excel::store(new SubscriptionExport, 'subscription/' . $this->data['file_name']);

            $emailTemplate = TemplateManagement::select('template', 'subject')
                ->where('slug', 'subscription_report')->first();
            $mailData = [
                'message' => $emailTemplate->template ?? '',
                'subject' => $emailTemplate->subject ?? '',
                'file_name' => $this->data['file_name'],
                'type' => config('constants.subscription_type'),
                'put' => $mailData['put'],
            ];

            $check = Mail::to(config('constants.admin_mail'))->send(new SendReportMail($mailData));

            //email sent success
            if ($check instanceof \Illuminate\Mail\SentMessage) {
                $path = storage_path() . config('constants.subscription_path') . $this->data['file_name'];
                unlink($path);
            }
        }
    }
}
