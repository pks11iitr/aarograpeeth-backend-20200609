@if($type=='clinic')
<form role="form" method="post" enctype="multipart/form-data" action="{{route('order.booking.edit', ['type'=>$type, 'id'=>$booking->id])}}">
@csrf
<input type="hidden" name="clinic_id" id="slot-clinic-id" value="{{$booking->clinic_id}}">
<div class="card-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleInputEmail1">Clinic Name</label>
                <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Clinic Name" disabled value="{{$booking->clinic->name??''}}">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Grade</label>
                <input type="text" name="grade" class="form-control" placeholder="Grade" disabled value="{{$booking->grade??''}}" id="booking-grade">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Time Slot</label>
                <select class="form-control" name="slot_id" id="time-slots" onchange="getAvailableTherapist()" required>
                    <option value="{{$booking->timeslot->id??''}}">{{isset($booking->timeslot)?($booking->timeslot->date.' '.$booking->timeslot->start_time):'Select Time Slot'}}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Status</label>
                <select class="form-control" name="status" required>
                    <option value="pending" @if($booking->status=='pending'){{'selected'}}@endif>Pending</option>
                    <option value="confirmed" @if($booking->status=='confirmed'){{'selected'}}@endif>Confirmed</option>
                    <option value="completed" @if($booking->status=='completed'){{'selected'}}@endif>Completed</option>
                    <option value="cancelled" @if($booking->status=='cancelled'){{'selected'}}@endif>Cancelled</option>
                </select>
            </div>



        </div>


        <div class="col-md-6">
        <div class="form-group">
            <label for="exampleInputEmail1">Therapy Name</label>
            <input type="text" name="city" class="form-control" id="exampleInputEmail3" placeholder="Enter City" value="{{$booking->therapy->name??''}}" disabled>
        </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Date</label>
                <input type="date" name="date" class="form-control" id="slot-date" placeholder="select Date" value="{{$booking->timeslot->date??''}}" onchange="getTimeSlotList()" required>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Therapist Name</label>
                <select class="form-control" name="therapist_id" required id="therapist-list">
                    <option value="{{$booking->assignedTo->id??''}}">{{$booking->assignedTo->name??'Select Therapist'}}</option>
                </select>
            </div>

    </div>
</div>
</div>
<!-- /.card-body -->
<div class="card-footer">
    <button type="submit" class="btn btn-primary">Submit</button>
</div>
</form>
@elseif($type=='home')
<form role="form" method="post" enctype="multipart/form-data" action="{{route('order.booking.edit', ['type'=>$type, 'id'=>$booking->id])}}">
        @csrf
        <input type="hidden" name="clinic_id" id="slot-therapy-id" value="{{$booking->therapy_id}}">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Therapy Name</label>
                        <input type="text" name="city" class="form-control" id="exampleInputEmail3" placeholder="Therapy Name" value="{{$booking->therapy->name??''}}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Date</label>
                        <input type="date" name="date" class="form-control" id="slot-date" placeholder="select Date" value="{{$booking->timeslot->date??''}}" onchange="getTimeSlotList1()" required>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending" @if($booking->status=='pending'){{'selected'}}@endif>Pending</option>
                            <option value="confirmed" @if($booking->status=='confirmed'){{'selected'}}@endif>Confirmed</option>
                            <option value="completed" @if($booking->status=='completed'){{'selected'}}@endif>Completed</option>
                            <option value="cancelled" @if($booking->status=='cancelled'){{'selected'}}@endif>Cancelled</option>
                        </select>
                    </div>



                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Grade</label>
                        <input type="text" name="grade" class="form-control" placeholder="Grade" disabled value="{{$booking->grade??''}}" id="booking-grade">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Time Slot @if(!isset($booking->slot_id))( Instant Booking/{{$booking->date}} )@endif</label>
                        <select class="form-control" name="slot_id" id="time-slots" onchange="getAvailableTherapist1()">
                            <option value="{{$booking->timeslot->id??''}}">{{isset($booking->timeslot)?($booking->timeslot->date.' '.$booking->timeslot->start_time):'Select Time Slot'}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Therapist Name</label>
                        <select class="form-control" name="therapist_id" required id="therapist-list">
                            <option value="{{$booking->assignedTo->id??''}}">{{$booking->assignedTo->name??'Select Therapist'}}</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endif
