<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Model\Applications;
use Mail;
use Illuminate\Support\Facades\Log;

class SendApprovalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approval:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reminder emails to all pending approvers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 🔹 Fetch all applications that still have pending approvers
        $applications = Applications::with([
            'esign_approver_details.user_details',
            'originator_details'
        ])
        // ->where('aidrc_control_number', '0825-788')
        ->where('status', 4)
        ->where('logdel', 0)
        ->get();

        foreach ($applications as $application){
            try {
                // Find the minimum approval_order among pending approvers
                $pendingApprovers = collect($application->esign_approver_details)
                                    ->filter(function ($item){
                                        return $item->status == 0 && is_null($item->deleted_at);
                                    });

                if ($pendingApprovers->isEmpty()){
                    // log and skip this application
                    // Log::warning("Skipped Application ID {$application->id} - no pending approvers");
                    $this->warn("Skipped Application ID {$application->id} - no pending approvers");
                    continue; // go to next application in the foreach loop
                }

                // get the approver with the smallest approval_order
                $currentApprover = $pendingApprovers->sortBy('approval_order')->first();

                // guard if somehow user_details is missing
                if (!$currentApprover || !$currentApprover->user_details){
                    // Log::warning("Skipped Application ID {$application->id} - approver missing user_details");
                    $this->warn("Skipped Application ID {$application->id} - approver missing user_details");
                    continue;
                }

                // $send_to = ['cdcasuyon@pricon.ph']; //current approver only
                // $send_cc = ['cdcasuyon@pricon.ph']; //current approver only

                $send_to = [$currentApprover->user_details->email]; //current approver only
                $send_cc = [optional($application->originator_details)->email];
                $data = ['application' => [$application]];

                if(!empty($send_to)){
                    Mail::send('mail.aidrc_new_application', $data, function ($message) use ($send_to, $send_cc) {
                        $message->to($send_to)
                                ->cc($send_cc)
                                ->bcc(['cdcasuyon@pricon.ph', 'dmmarmol@pricon.ph'])
                                ->subject('AIDRCV2: Reminder - Application for Approval');
                    });

                    $this->info("Reminder sent for Application ID: {$application->id}, Control Number: {$data['application'][0]->aidrc_control_number}");
                }
            }catch(\Throwable $e){
                // Log::error("Error in Application ID {$application->id}: " . $e->getMessage());
                $this->error("Skipped Application ID {$application->id} - error occurred: " . $e->getMessage());
                continue;
            }
        }
    }
}
