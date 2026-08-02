<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Models\ContractPaymentSchedule;
use App\Models\User;
use App\Notifications\PaymentAlertNotification;
use Illuminate\Support\Facades\Notification;

#[Signature('app:check-overdue-payments')]
#[Description('Check for overdue payments and send notifications')]
class CheckOverduePayments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overduePayments = ContractPaymentSchedule::where(function($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function($sub) {
                      $sub->whereIn('status', ['pending', 'unpaid'])
                          ->where('due_date', '<', now()->startOfDay());
                  });
            })->get();

        if ($overduePayments->count() > 0) {
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            
            foreach ($overduePayments as $payment) {
                Notification::send($admins, new PaymentAlertNotification($payment, 'overdue'));
            }
            
            $this->info("Sent notifications for {$overduePayments->count()} overdue payments.");
        } else {
            $this->info("No overdue payments found.");
        }
    }
}
