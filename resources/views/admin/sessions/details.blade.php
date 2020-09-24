@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapy Sessions Details</h1>
                    </div>
{{--                    <div class="col-sm-6">--}}
{{--                        <ol class="breadcrumb float-sm-right">--}}
{{--                            <li class="breadcrumb-item"><a href="#">Home</a></li>--}}
{{--                            <li class="breadcrumb-item active"><a href="{{route('orders.list')}}">Order Details</a></li>--}}
{{--                        </ol>--}}
{{--                    </div>--}}
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
                                <h4>Session</h4>
                            </div>
                            <!-- /.card-header -->
                            <!-- /.card-body -->
                                <div class="card-body">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
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


                                                <tr>
                                                    <td>SESSION{{$session->id??''}}</td>
                                                    <td>{{$session->grade??''}}</td>
                                                    <td>{{$session->price??''}}</td>
                                                    <td>{{$session->timeslot->date??$session->date}}</td>
                                                    <td>{{$session->timeslot->start_time??$session->time}}</td>
                                                    <td>{{$session->assignedTo->name??''}}</td>
                                                    <td>{{$session->status}}</td>
                                                    <td>@if($session instanceof App\Models\BookingSlot)
                                                        <a href="javascript:void(0)" onclick="getBooking({{$session->id}}, 'home')">Edit</a>
                                                        @elseif($session instanceof App\Models\HomeBookingSlots)
                                                            <a href="javascript:void(0)" onclick="homegetBooking({{$session->id}}, 'home')">Edit</a>
                                                        @endif
                                                    </td>
                                                </tr>



                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>

                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>Customer</h4>
                            </div>
                            <!-- /.card-header -->
                            <!-- /.card-body -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    <tr>
                                        <td>{{$session->order->name??''}}</td>
                                        <td>{{$session->order->email??''}}</td>
                                        <td>{{$session->order->mobile??''}}</td>
                                        <td>{{$session->order->address??''}}</td>
                                    </tr>



                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>



                        </div>
                        <!-- /.card -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Treatment Details</h4>
                            </div>
                            <!-- /.card-header -->
                            <!-- /.card-body -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Pain Points</th>
                                        <th>Disease</th>
                                        <th>Treatment</th>
                                        <th>Relief Ratings</th>
                                        <th>Feedback</th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    <tr>
                                        <td>@foreach($session->painpoints as $p){{$p->name??''}}<br>@endforeach</td>
                                        <td>@foreach($session->diseases as $p){{$p->name??''}}<br>@endforeach</td>
                                        <td>{{$session->treatment->name??''}}</td>
                                        <td>@foreach($session->painpoints as $p){{$p->name??''}}:&nbsp{{$p->pivot->related_rating??''}}<br>@endforeach</td>
                                        <td>{{$session->comments??''}}<br></td>
                                    </tr>



                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>



                        </div>

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


        function getTimeSlotList1(){
            $("#therapist-list").html('')
            $("#time-slots").html('')
            $.ajax({

                url: '{{route('therapy.available.slots')}}',
                method: 'get',
                datatype:'json',
                data:{therapy_id:$("#slot-therapy-id").val(), date:$("#slot-date").val(), grade:$("#booking-grade").val()},
                success: function(data){
                    html='<option value="">Select Time</option>'
                    for(var i = 0; i < data.length; i++) {
                        if(data[i].is_active==1){
                            html=html+'<option value="'+data[i].id+'">'+data[i].date+' '+data[i].display+'</option>'
                        }else{
                            html=html+'<option value="'+data[i].id+'" disabled>'+data[i].date+' '+data[i].display+'</option>'
                        }

                    }

                    $("#time-slots").html(html)
                },

            });

        }

        function getAvailableTherapist1(){
            $("#therapist-list").html('')
            $.ajax({

                url: '{{route('therapy.available.therapist')}}',
                method: 'get',
                datatype:'json',
                data:{therapy_id:$("#slot-therapy-id").val(), slot_id:$("#time-slots").val(), },
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
