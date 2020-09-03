<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    protected $table='reschedule_requests';

    protected $fillable=['order_id', 'booking_id', 'old_slot_id', 'new_slot_id', 'razorpay_order_id', 'razorpay_order_id_response', 'payment_id', 'payment_id_response','is_paid', 'total_cost', 'refid', 'use_balance', 'balance_used'];

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
