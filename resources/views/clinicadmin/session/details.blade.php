@extends('layouts.clinicadmin')
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
                                                <a href="javascript:void(0)" onclick="getBooking({{$session->id}}, 'clinic')">Edit</a>
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

                        <div class="card">
                            <div class="card-header">
                                <h4>Customer Diseases</h4>
                            </div>
                            <!-- /.card-header -->
                            <!-- /.card-body -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Main Diseases</th>
                                        <th>Pain Points</th>
                                        <th>Disease</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            @foreach($session->mainDiseases as $md)
                                                {{$loop->iteration}}. {{$md->name??''}}--
                                                @foreach($session->reasonDiseases as $rd)
                                                    @if($rd->pivot->disease_id==$md->id)
                                                        {{$rd->name}},
                                                    @endif
                                                @endforeach
                                                <br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($session->painpoints as $p){{$p->name??''}}:&nbsp<br>
                                            @endforeach
                                        </td>
                                        <td>@foreach($session->diseases as $d){{$d->name??''}}<br>@endforeach</td>
                                    </tr>



                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>



                        </div>
                        <!-- /.card -->
                        <!-- /.card -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Treatment Details</h4>
                            </div>
                            <!-- /.card-header -->
                            <!-- /.card-body -->
                            @foreach($session->treatmentsGiven as $t)
                                <div class="card-body">
                                    <dl><h5><b>{{$loop->iteration}}. Treatment</b></h5>
                                        <dt>Formulae</dt>
                                        <dd>
                                            {{$t->description}}
                                        </dd>
                                        <dt>Exercise</dt>
                                        <dd>
                                            {{$t->exercise}}
                                        </dd>

                                        <dt>Dont Exercise</dt>
                                        <dd>
                                            {{$t->dont_exercise}}
                                        </dd>

                                        <dt>Diet</dt>
                                        <dd>
                                            {{$t->diet}}
                                        </dd>

                                        <dt>Recommended Days</dt>
                                        <dd>
                                            {{$t->recommended_days}}
                                        </dd>

                                        <dt>What to do if pain increase?</dt>
                                        <dd>
                                            {{$t->action_when_pain_increase}}
                                        </dd>
                                    </dl>
                                </div>
                            @endforeach

                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Other Diagnose</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h6>Diagnose Points:</h6>
                                            @foreach($session->diagnose as $dp)
                                                <div class="form-check">
                                                    <label class="form-check-label" for="exampleCheck1">{{$dp->name}}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @php
                                        $diagnosed_values=[];
                                        foreach($session->diagnose as $d){
                                            if(!isset($diagnosed_values[$d->id]))
                                            $diagnosed_values[$d->id]=[];
                                            $diagnosed_values[$d->id]['before_value']=$d->pivot->before_value;
                                            $diagnosed_values[$d->id]['after_value']=$d->pivot->after_value;
                                        }
                                    @endphp
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h6>Before Treatment</h6>
                                            @foreach($session->diagnose as $dp)
                                                <div class="form-check">
                                                    {{$dp->pivot->before_value??'--'}}
                                                    <label class="form-check-label" for="exampleCheck1"></label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <!-- One "tab" for each step in the form: -->
                                            <h6>After Treatment</h6>
                                            @foreach($session->diagnose as $dp)
                                                <div class="form-check">
                                                    {{$dp->pivot->after_value??'--'}}
                                                    <label class="form-check-label" for="exampleCheck1"></label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>Treatment Result</h4>
                            </div>
                            <!-- /.card-header -->
                            <!-- /.card-body -->
                            <div class="card-body">
                                <dl>
                                    <dt>Result</dt>
                                    <dd>
                                        {{$session->results()}}
                                    </dd>
                                    <dt>Therapist Comments</dt>
                                    <dd>
                                        {{$session->message}}
                                    </dd>
                                </dl>
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

    @include('clinicadmin.session.session-js')


@endsection
