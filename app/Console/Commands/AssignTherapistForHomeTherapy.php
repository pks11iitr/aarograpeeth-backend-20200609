<?php

namespace App\Console\Commands;

use App\Models\HomeBookingSlots;
use App\Models\Therapist;
use Illuminate\Console\Command;

class AssignTherapistForHomeTherapy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homebooking:assigntherapist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $homebookings=HomeBookingSlots::with('order')
            ->whereHas('order', function($order){
                $order->where('orders.status', 'confirmed');
            })
            ->where('is_confirmed', true)
            ->where('status', 'pending')
            ->where('date', date('Y-m-d'))
            ->get();

        foreach($homebookings as $booking){
            if($booking->is_instant){
                $this->assignInstantTherapist($booking);
            }else{
                $this->assignScheduledTherapist($booking);
            }
        }

    }


    private function assignInstantTherapist($booking){
        if(!$booking->assigned_therapist){
            /*
             * No therapist has been assigned
             * Find available therapist and assign to booking
             */
            $therapists=Therapist::getAvailableHomeTherapist($booking->therapy_id, null, $booking->order->lat, $booking->order->lang);

            foreach($therapists as $therapist){
                $booking->assigned_therapist=$therapist->id;
                $booking->assigned_at=date('Y-m-d H:i:s');
                $booking->save();
                echo 'therapist id'.$therapist->id.' assigned to '.$booking->id;
                break;
            }

        }else{
            /*
             * Therapist has been assigned
             * Change Therapist if therapist is not accepting booking
             * in given time frame
             */
            if(strtotime('now') - strtotime($booking->assigned_at) >= 300){
                $therapists=Therapist::getAvailableHomeTherapist($booking->therapy_id, null, $booking->order->lat, $booking->order->lang);
                $booking->refresh();
                if($booking->status=='pending'){
                    foreach($therapists as $therapist){
                        $booking->assigned_therapist=$therapist->id;
                        $booking->assigned_at=date('Y-m-d H:i:s');
                        $booking->save();
                        echo 'therapist id'.$therapist->id.' assigned to '.$booking->id;
                        break;
                    }
                }
            }
        }
    }


    private function assignScheduledTherapist($booking){
        if(!$booking->assigned_therapist){
            /*
             * No therapist has been assigned
             * Find available therapist and assign to booking
             */
            $therapists=Therapist::getAvailableHomeTherapist($booking->therapy_id, $booking->slot_id, $booking->order->lat, $booking->order->lang);
            if(strtotime($booking->date.' '.$booking->time)-strtotime('now') < 120*60){
                foreach($therapists as $therapist){
                    $booking->assigned_therapist=$therapist->id;
                    $booking->assigned_at=date('Y-m-d H:i:s');
                    $booking->save();
                    echo 'therapist id'.$therapist->id.' assigned to '.$booking;
                    break;
                }
            }
        }else{
            /*
             * Therapist has been assigned
             * Change Therapist if therapist is not accepting booking
             * in given time frame
             */
            if(strtotime('now') - strtotime($booking->assigned_at) >= 300){
                $therapists=Therapist::getAvailableHomeTherapist($booking->therapy_id, $booking->slot_id, $booking->order->lat, $booking->order->lang);
                $booking->refresh();
                if($booking->status=='pending') {
                    foreach ($therapists as $therapist) {
                        $booking->assigned_therapist = $therapist->id;
                        $booking->assigned_at = date('Y-m-d H:i:s');
                        $booking->save();
                        echo 'therapist id'.$therapist->id.' assigned to '.$booking;
                        break;
                    }
                }
            }
        }
    }
}
