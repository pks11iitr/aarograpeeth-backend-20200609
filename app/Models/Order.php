<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    /*
     * List of order status:
     * Products: pending, confirmed, cancelled, processing, return-requested, 'returned', 'completed'
     * Therapies: pending, confirmed, cancelled, processing, completed
     */
    protected $table='orders';

    protected $fillable=[ 'refid', 'total_cost', 'status', 'payment_status', 'payment_mode', 'order_details_completed', 'booking_date', 'booking_time', 'user_id', 'name', 'email', 'mobile', 'address', 'lat', 'lang', 'is_instant', 'use_wallet', 'use_points', 'balance_used', 'points_used','schedule_type','order_place_state'];

    public function details(){
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }

    public function getOrderDescription(){

    }

    public function schedule(){
        return $this->hasMany('App\Models\BookingSlot', 'order_id');
    }

    public function payments(){
        return $this->belongsTo('App\Models\Payments', 'order_id');
    }

    public function bookingSlots(){
        return $this->hasMany('App\Models\BookingSlot', 'order_id');
    }

    public function homebookingslots(){
        return $this->hasMany('App\Models\HomeBookingSlots', 'order_id');
    }


    public static function getTotal(Order $order){
        $cost=0;
        if($order->details[0]->entity_type=='App\Models\Therapy')
        {
            if($order->details[0]->clinic_id){

                $clinic=Clinic::active()->with(['therapies'=>function($therapies)use($order){
                    $therapies->where('therapies.isactive', true)->where('therapies.id', $order->details[0]->entity_id);
                }])->find($order->details[0]->clinic_id);

                $bookings=$order->bookingSlots;
                foreach($bookings  as $booking){
                    switch($booking->grade){
                        case 1:$cost=$cost+($clinic->therapies[0]->pivot->grade1_price??0);
                            break;
                        case 2:$cost=$cost+($clinic->therapies[0]->pivot->grade2_price??0);
                            break;
                        case 3:$cost=$cost+($clinic->therapies[0]->pivot->grade3_price??0);
                            break;
                        case 4:$cost=$cost+($clinic->therapies[0]->pivot->grade4_price??0);
                            break;
                    }
                }
            }else{

                $therapy=Therapy::find($order->details[0]->entity_id);
                $bookings=$order->homebookingslots;
                foreach($bookings  as $booking) {
                    switch ($booking->grade) {
                        case 1:
                            $cost = $cost+($therapy->grade1_price ?? 0);
                            break;
                        case 2:
                            $cost = $cost+($therapy->grade2_price ?? 0);
                            break;
                        case 3:
                            $cost = $cost+($therapy->grade3_price ?? 0);
                            break;
                        case 4:
                            $cost = $cost+($therapy->grade4_price ?? 0);
                            break;
                    }
                }
            }
        }else{
            foreach($order->details as $d){
                $cost=$cost+$d->entity->price;
            }
        }

        return $cost;


    }


}
