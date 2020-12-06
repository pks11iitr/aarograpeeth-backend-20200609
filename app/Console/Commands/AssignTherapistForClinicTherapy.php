<?php

namespace App\Console\Commands;

use App\Models\BookingSlot;
use App\Models\Clinic;
use Illuminate\Console\Command;

class AssignTherapistForClinicTherapy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        // assign therapist for todays bookings
        $bookings=BookingSlot::where('is_confirmed', true)
            ->whereHas('order', function($order){
                $order->where('orders.status', 'confirmed');
            })
            ->where('status', 'pending')
            ->where('assigned_therapist', null)
            ->where('date', date('Y-m-d'))
            ->get();
        foreach($bookings as $booking){
            $clinic=Clinic::findOrFail($booking->clinic_id);//die;
            $therapists=$clinic->getAvailableTherapist($booking->slot_id);
            if(count($therapists)){
                $booking->assigned_therapist=$therapists[0]->id;
                $booking->save();
            }
        }
    }
}
