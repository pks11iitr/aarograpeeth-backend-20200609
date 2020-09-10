@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Customers</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('orders.list')}}">Order Details</a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Order Details</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Order ID</td>
                                        <td>{{$order->refid}}</td>
                                    </tr>
                                    <tr>
                                        <td>Date & Time</td>
                                        <td>{{$order->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td>Therapy Name</td>
                                        <td>{{$order->details[0]->entity->name??'-'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Clinic Name</td>
                                        <td>{{$order->details[0]->clinic->name??'-'}}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>{{$order->total_cost}}</td>
                                    </tr>
                                    <tr>
                                        <td>Payment Status</td>
                                        <td>{{$order->payment_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{$order->status}}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
{{--                            <div class="card-body">--}}
{{--                                <table id="example2" class="table table-bordered table-hover">--}}
{{--                                    <thead>--}}
{{--                                    <tr>--}}
{{--                                        @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)--}}
{{--                                            <th>Therapy Details</th>--}}
{{--                                            @else--}}
{{--                                            <th>Product Details</th>--}}
{{--                                            @endif--}}
{{--                                        <th></th>--}}
{{--                                        <th></th>--}}
{{--                                        <th></th>--}}
{{--                                    </tr>--}}
{{--                                    </thead>--}}
{{--                                    <tbody>--}}
{{--                                    @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)--}}
{{--                                        @foreach($order->details as $detail)--}}
{{--                                        <tr>--}}
{{--                                            <td>{{$detail->entity->name??''}}</td>--}}
{{--                                            <td>{{$detail->clinic->name??''}}</td>--}}
{{--                                            <td>Grade {{$detail->grade??''}}</td>--}}
{{--                                            <td>Sessions: {{$detail->quantity}}</td>--}}

{{--                                            <td>Rs. {{$detail->cost}}/session</td>--}}
{{--                                        </tr>--}}
{{--                                        @endforeach--}}
{{--                                    @else--}}
{{--                                        @foreach($order->details as $detail)--}}
{{--                                            <tr>--}}
{{--                                                <td>{{$detail->entity->name??''}}</td>--}}
{{--                                                <td>Quantity: {{$detail->quantity}}</td>--}}
{{--                                                <td>Rs. {{$detail->cost}}/Item</td>--}}
{{--                                                <td>Rs. {{$detail->cost*$detail->quantity}} Total</td>--}}

{{--                                            </tr>--}}
{{--                                        @endforeach--}}
{{--                                    @endif--}}
{{--                                    </tbody>--}}
{{--                                    <tfoot>--}}
{{--                                    </tfoot>--}}
{{--                                </table>--}}
{{--                            </div>--}}
                            @if($order->details[0]->entity_type=='App\Models\Therapy')
                        <!-- /.card-body -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Grade</th>
                                        <th>Price</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Therapist</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($order->details[0]->clinic_id) )

                                        @foreach($order->bookingSlots()->with(['timeslot', 'assignedTo'])->get() as $bookingSlot)
                                            <tr>
                                                <td>{{$bookingSlot->grade??''}}</td>

                                                <td>Rs. {{$bookingSlot->price??''}}</td>                                               <td>{{$bookingSlot->timeslot->date??''}}</td>
                                                <td>{{$bookingSlot->timeslot->start_time??''}}</td>
                                                <td>{{$bookingSlot->assignedTo->name??''}}</td>
                                                <td>{{$bookingSlot->status}}</td>
                                                <td><a href="javascript:void(0)" onclick="getBooking({{$bookingSlot->id}}, 'clinic')">Edit</a></td>
                                            </tr>
                                        @endforeach

                                    @else
                                        @foreach($order->homebookingslots as $homebookingslot)
                                            <tr>
                                                <td>{{$homebookingslot->grade??''}}</td>
                                                <td>{{$homebookingslot->price??''}}</td>
                                                <td>{{$homebookingslot->timeslot->date??''}}</td>
                                                <td>{{$homebookingslot->timeslot->start_time??''}}</td>
                                                <td></td>
                                                <td>{{$homebookingslot->status}}</td>
                                                <td><a href="javascript:void(0)" onclick="getBooking({{$homebookingslot->id}}, 'home')">Edit</a></td>
                                            </tr>
                                        @endforeach

                                    @endif
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            @endif
                            <!-- /.card -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Customer Details</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>{{$order->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Mobile</td>
                                        <td>{{$order->mobile}}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{$order->email}}</td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td>{{$order->adderss}}</td>
                                    </tr>
                                    @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)
                                        <tr>
                                            <td>Booking Date</td>
                                            <td>{{$order->booking_date}}</td>
                                        </tr>
                                        <tr>
                                            <td>Booking Time</td>
                                            <td>{{$order->booking_time}}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.card -->

                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <div class="modal fade show" id="modal-lg" style="display: none; padding-right: 15px;" aria-modal="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Booking Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="booking-form-section">

                </div>
{{--                <div class="modal-footer justify-content-between">--}}
{{--                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
{{--                    <button type="button" class="btn btn-primary">Save changes</button>--}}
{{--                </div>--}}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection
@section('scripts')

    <script>
    function getBooking(id, type)
    {
        $("#booking-form-section").html('')
        $.ajax({

            url: '{{route('order.booking.edit')}}',
            method: 'get',
            data:{type:type, id:id},
            success: function(data){
                $("#booking-form-section").html(data)
                $("#modal-lg").modal('show')
            },

        });
    }

    function getTimeSlotList(){
        $("#therapist-list").html('')
        $("#time-slots").html('')
        $.ajax({

            url: '{{route('clinic.available.slots')}}',
            method: 'get',
            datatype:'json',
            data:{clinic_id:$("#slot-clinic-id").val(), date:$("#slot-date").val(), grade:$("#booking-grade").val()},
            success: function(data){
                html='<option value="">Select Time</option>'
                for(var i = 0; i < data.length; i++) {
                    if(data[i].is_active==1){
                        html=html+'<option value="'+data[i].id+'">'+data[i].date+' '+data[i].start_time+'</option>'
                    }else{
                        html=html+'<option value="'+data[i].id+'" disabled>'+data[i].date+' '+data[i].start_time+'</option>'
                    }

                }

                $("#time-slots").html(html)
            },

        });

    }

    function getAvailableTherapist(){
        $("#therapist-list").html('')
        $.ajax({

            url: '{{route('clinic.available.therapist')}}',
            method: 'get',
            datatype:'json',
            data:{clinic_id:$("#slot-clinic-id").val(), slot_id:$("#time-slots").val(), },
            success: function(data){
                html='<option value="">Select Therapist</option>'
                for(var i = 0; i < data.length; i++) {

                        html=html+'<option value="'+data[i].id+'">'+data[i].name+'</option>'

                }

                $("#therapist-list").html(html)
            },

        });
    }


    </script>


@endsection
