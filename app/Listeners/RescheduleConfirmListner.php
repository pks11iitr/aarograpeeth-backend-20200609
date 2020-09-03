<?php

namespace App\Listeners;

use App\Events\RescheduleConfirmed;
use App\Models\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RescheduleConfirmListner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RescheduleConfirmed  $event
     * @return void
     */
    public function handle(RescheduleConfirmed $event)
    {
        $order=$event->order;
        $user=$event->user;

        Notification::create([
            'user_id'=>$user->id,
            'title'=>'Reschedule Confirmed',
            'description'=>'Your Booking Reschedule Request Has Been Approved',
            'data'=>null,
            'type'=>'individual'
        ]);
    }
}
