<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Borrower;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanApplicationWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $borrower;
    public $isNewUser;
    public $tempPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Borrower $borrower, bool $isNewUser = false, string $tempPassword = null)
    {
        $this->user = $user;
        $this->borrower = $borrower;
        $this->isNewUser = $isNewUser;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->isNewUser
            ? 'Welcome to Our Loan Platform - Your Application Has Been Submitted'
            : 'Your Loan Application Has Been Submitted';

        return $this->subject($subject)
            ->view('emails.loan-application-welcome')
            ->with([
                'user' => $this->user,
                'borrower' => $this->borrower,
                'isNewUser' => $this->isNewUser,
                'tempPassword' => $this->tempPassword,
                'loginUrl' => route('login'),
            ]);
    }
}