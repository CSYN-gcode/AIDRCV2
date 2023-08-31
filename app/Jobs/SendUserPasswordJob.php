<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\SendUserPassword;
use Mail;

class SendUserPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $subject;
    public $message;
    public $username;
    public $password;
    public $email;

    public function __construct($subject, $message, $username, $password, $email)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userPassMail = new SendUserPassword($this->subject, $this->message, $this->username, $this->password);

        Mail::to($this->email)->send($userPassMail);
    }
}
