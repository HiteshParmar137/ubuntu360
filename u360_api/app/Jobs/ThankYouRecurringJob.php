<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\ProjectDonation;
use Illuminate\Support\Facades\DB;

/**
 * Create a new job instance.
 *
 * @return void
 */

class ThankYouRecurringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    /* Global Variable intialiation */
    public $pid;

    public function __construct($id)
    {
        $this->pid   = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $project = Project::select('amount', 'title')->where('id', $this->pid)->where('is_donation_reached', '0');
        $project = $project->withCount([
            'hasDonations as totalAmount' => function ($transaction) {
                $transaction->where('status', config('constants.paid_status'))
                    ->select(DB::raw('SUM(donation_amount)'));
            }
        ]);

        $project = $project->first();
        
        if (!empty($project) && $project->totalAmount >= $project->amount) {
            // start thank you email
            $userDetails = ProjectDonation::with('hasUser')
                ->where('project_id', $this->pid)
                ->where('status', config('constants.paid_status'))
                ->groupBy('user_id')
                ->get();

            $response = [];
            foreach ($userDetails as $userDetail) {
                $projectUsersArr = [];
                $projectUsersArr['id'] = !empty($userDetail->hasUser) ?
                    $userDetail->hasUser->id : '';
                $projectUsersArr['user_name'] = !empty($userDetail->hasUser)
                    ? $userDetail->hasUser->name : '';
                $projectUsersArr['user_email'] = !empty($userDetail->hasUser)
                    ? $userDetail->hasUser->email : '';
                $response[] = $projectUsersArr;
            }

            foreach ($response as $email) {
                $mailBody = getTemplateInfo('thankyou', 'email', [
                    '##USERNAME##' => $email['user_name'] ?? '',
                    '##PROJECT_TITLE##' => !empty($project->title) ? $project->title : 'No Project Title',
                ]);

                $mailData = [
                    'subject' => $mailBody['subject'],
                    'message' => $mailBody['message'],
                ];

                Mail::send(
                    'emails.thankyou',
                    ['mailData' => $mailData],
                    function ($message) use ($email, $mailData) {
                        $message->to(convertStringToLowercase($email['user_email']));
                        $message->subject($mailData['subject']);
                    }
                );
            }
            // end thank you

            // start recurring mail
            $userDetailRecurrings = ProjectDonation::with('hasUser')
                ->where('project_id', $this->pid)
                ->where('status', config('constants.paid_status'))
                ->where('donation_type', '2')
                ->where('is_recurring_stop', '0')
                ->groupBy('user_id')
                ->get();

            $responseRecurring = [];
            foreach ($userDetailRecurrings as $userDetailRecurring) {
                $projectUsersArr = [];
                $projectUsersArr['id'] = !empty($userDetailRecurring->hasUser) ?
                    $userDetailRecurring->hasUser->id : '';
                $projectUsersArr['user_name'] = !empty($userDetailRecurring->hasUser)
                    ? $userDetailRecurring->hasUser->name : '';
                $projectUsersArr['user_email'] = !empty($userDetailRecurring->hasUser)
                    ? $userDetailRecurring->hasUser->email : '';
                $responseRecurring[] = $projectUsersArr;
            }

            foreach ($responseRecurring as $email) {
                $mailBody = getTemplateInfo('reccuring', 'email', [
                    '##USERNAME##' => $email['user_name'] ?? '',
                    '##PROJECT_TITLE##' => !empty($project->title) ? $project->title : 'No Project Title',
                ]);

                $mailData = [
                    'subject' => $mailBody['subject'],
                    'message' => $mailBody['message'],
                ];

                Mail::send(
                    'emails.reccuring',
                    ['mailData' => $mailData],
                    function ($message) use ($email, $mailData) {
                        $message->to(convertStringToLowercase($email['user_email']));
                        $message->subject($mailData['subject']);
                    }
                );
            }
            Project::where('id', $this->pid)
            ->update([
                'is_donation_reached' => '1',
            ]);
        }
    }
}
