<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Exports\ProjectReportExport;
use App\Exports\UserReportExport;
use App\Mail\SendProjectReportMail;
use App\Models\TemplateManagement;

/**
 * Create a new job instance.
 *
 * @return void
 */

class ExportProjectReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    /* Global Variable intialiation */
    public $data = [];
    protected $input = [];

    public function __construct(array $data, array $input)
    {
        $this->data   = $data;
        $this->input  = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailData = [];
        if ($this->data['type'] == config('constants.project_type')) {
            $mailData['put'] = Excel::store(
                new ProjectReportExport($this->input),
                'project_report/' . $this->data['file_name']
            );

            $emailTemplate = TemplateManagement::select('template', 'subject')
                ->where('slug', 'project_report')->first();

            $mailData = [
                'message' => $emailTemplate->template ?? '',
                'subject' => $emailTemplate->subject ?? '',
                'file_name' => $this->data['file_name'],
                'type' => config('constants.project_type'),
                'put' => $mailData['put'],
            ];

            $check = Mail::to(config('constants.admin_mail'))->send(
                new SendProjectReportMail($mailData)
            );

            //email sent success
            if ($check instanceof \Illuminate\Mail\SentMessage) {
                $path = storage_path() . config('constants.project_report_path') . $this->data['file_name'];
                unlink($path);
            }
        } elseif ($this->data['type'] == config('constants.user_type')) {
            $mailData['put'] = Excel::store(
                new UserReportExport($this->input),
                'user_report/' . $this->data['file_name']
            );

            $emailTemplate = TemplateManagement::where('slug', 'user_report')->first();

            $mailData = [
                'message' => $emailTemplate->template ?? '',
                'subject' => $emailTemplate->subject ?? '',
                'file_name' => $this->data['file_name'],
                'type' => config('constants.user_type'),
                'put' => $mailData['put'],
            ];

            $check = Mail::to(config('constants.admin_mail'))->send(
                new SendProjectReportMail($mailData)
            );

            //email sent success
            if ($check instanceof \Illuminate\Mail\SentMessage) {
                $path = storage_path() . config('constants.user_path') . $this->data['file_name'];
                unlink($path);
            }
        }
    }
}
