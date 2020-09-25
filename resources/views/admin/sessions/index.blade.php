@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>All Sessions</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">DataTables</li>
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
                                <div class="row">
                                    <div class="col-12">

                                        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                                            <div class="row">
                                                @if($type=='clinic')
                                                <div class="col-4">

                                                    <select id="clinic" name="clinic_id" class="form-control" >

                                                        <option value="" {{ request('status')==''?'selected':''}}>Select Clinic</option>
                                                        @foreach($clinics as $c)
                                                            <option value="{{$c->id}}" {{ request('clinic_id')==$c->id?'selected':''}}>{{$c->name}}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                                @endif
                                                <div class="col-4">

                                                    <select id="therapy_id" name="therapy_id" class="form-control" >

                                                        <option value="" {{ request('therapy_id')==''?'selected':''}}>Select Therapy</option>
                                                        @foreach($therapies as $therapy)
                                                            <option value="{{$therapy->id}}" {{ request('therapy_id')==$therapy->id?'selected':''}}>{{$therapy->name}}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                                <div class="col-4">

                                                    <select id="therapist_id" name="therapist_id" class="form-control" >

                                                        <option value="" {{ request('therapist_id')==''?'selected':''}}>Select Therapist</option>
                                                        @foreach($therapists as $therapist)
                                                            <option value="{{$therapist->id}}" {{ request('therapist_id')==$therapist->id?'selected':''}}>{{$therapist->name}}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                                <div class="col-4">
                                                    <select id="status" name="status" class="form-control" >

                                                        <option value="" {{ request('status')==''?'selected':''}}>Selected Status</option>
                                                        <option value="pending" {{ request('status')=='pending'?'selected':''}}>Pending</option>
                                                        <option value="completed" {{ request('status')==='completed'?'selected':''}}>Completed</option>

                                                    </select>

                                                </div><br><br>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                                                </div>
                                                <div class="col-4">
                                                    <input  id="fullname"  class="form-control" name="todate" placeholder=" search name" value="{{request('todate')}}"  type="date" />
                                                </div>
                                                <div class="col-4">
                                                    <button type="submit" name="save" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Session</th>
                                        <th>Therapy</th>
                                        <th>Clinic</th>
                                        <th>Therapist</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($sessions as $session)
                                        <tr>
                                            <td><a href="{{route('session.details', ['type'=>'therapist-session', 'id'=>$session->id])}}">SESSION{{$session->id??''}}</td>
                                            <td>{{$session->therapy->name??''}}</td>
                                            <td>{{$session->clinic->name??''}}</td>
                                            <td>{{$session->assignedTo->name??''}}</td>
                                            <td>{{$session->timeslot->date??''}}</td>
                                            <td>{{$session->timeslot->start_time??''}}</td>
                                            <td>{{$session->status}}</td>
                                            <td><a href="javascript:void(0)" onclick="getBooking({{$session->id}}, '{{$type}}')">Edit</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Session</th>
                                        <th>Therapy</th>
                                        <th>Clinic</th>
                                        <th>Therapist</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        {{$sessions->appends(request()->query())->links()}}
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
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

                url: '{{route('therapist.booking.edit')}}',
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


