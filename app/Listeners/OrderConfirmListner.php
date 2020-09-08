<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Models\Notification;
use App\Services\Notification\FCMNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderConfirmListner
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
     * @param  OrderConfirmed  $event
     * @return void
     */
    public function handle(OrderConfirmed $event)
    {
        $order=$event->order;

        $this->sendNotifications($order);

    }


    public function sendNotifications($order){

        $message='';
        if($order->details[0]->entity_type == 'App\Models\Product'){
            $message='Congratulations! Your purchase of Rs. '.$order->total_cost.' at Arogyapeeth.com is successfull. Order Reference ID: '.$order->refid;
        }else{
            $message='Congratulations! Your therapy booking of Rs. '.$order->total_cost.' at Arogyapeeth.com is successfull. Order Reference ID: '.$order->refid;

        }

        $user=$order->customer;

        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>'Order Confirmed',
            'description'=>$message,
            'data'=>null,
            'type'=>'individual'
        ]);

        FCMNotification::sendNotification($user->notification_token, 'Order Confirmed', $message);
    }
}
