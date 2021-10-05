@extends('layouts.clinicadmin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapists</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Timeslots</li>
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
                                    <div class="col-3">
                                        <a href="{{route('clinic.timeslots.add')}}" class="btn btn-primary">Add Timeslots</a> </div>
                                    <div class="col-3">
                                        <a href="{{route('clinic.timeslots.repeat')}}" class="btn btn-primary">Repeat Previous Day Slots</a> </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        {{--                                        <th>Bookings</th>--}}
                                        <th>Time</th>
                                        {{--                                        <th>mobile</th>--}}
                                        {{--                                        <th>city</th>--}}
                                        {{--                                        <th>state</th>--}}
                                        <th>Duration</th>
                                        <th>Grade 1</th>
                                        <th>Grade 2</th>
                                        <th>Grade 3</th>
                                        <th>Grade 3</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($timeslots as $slot)
                                        <tr>
                                            <td>{{$slot->date}}</td>
                                            {{--                                            <td>{{$therapist->bookings()->where('status', 'completed')->count()}}</td>--}}
                                            <td>{{$slot->start_time}}</td>
                                            {{--                                            <td>{{$therapist->mobile}}</td>--}}
                                            <td>{{$slot->duration}}</td>
                                            {{--                                            <td>{{$therapist->city}}</td>--}}
                                            {{--                                            <td>{{$therapist->state}}</td>--}}
                                            <td>{{$slot->grade_1}}</td>
                                            <td>{{$slot->grade_2}}</td>
                                            <td>{{$slot->grade_3}}</td>
                                            <td>{{$slot->grade_4}}</td>
                                            <td>{{$slot->isactive==1?'Active':'Disabled'}}</td>
                                            <td><a href="{{route('clinic.timeslots.deactivate',['id'=>$slot->id, 'status'=>$slot->isactive??0])}}" class="btn btn-primary">{{$slot->isactive==1?'Disable':'Activate'}}</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                        {{$timeslots->links()}}
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
@endsection

