@extends('layouts.therapistadmin')

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Therapist </a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Therapist work</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Therapist Name</th>
                                    <th>Home Booking Time</th>
                                    <th>Home Booking Date</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($openbookings as $openbooking)
                                    <tr>
                                        <td>
                                            {{$openbooking->therapieswork->therapiesorder->refid??''}}</td>
                                        <td>
                                            {{$openbooking->therapieswork->therapiesorder->details[0]->entity->name??''}}
                                        </td>
                                        <td>{{$openbooking->therapieswork->time??''}}</td>
                                        <td>{{$openbooking->therapieswork->date??''}}</td>
                                        <td>{{$openbooking->message}}</td>
                                        <td>{{$openbooking->status}}</td>
                                        <td>{{$openbooking->created_at}}</td>
                                        <td><a href="{{route('therapistwork.details',['id'=>$openbooking->id])}}" class="btn btn-success">Details</a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-header -->



                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

@endsection
