@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Clinic</h1>
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
                                    <div class="col-3">
                                        <h3 class="card-title">Time Slots</h3>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-8">
                                        <form role="form" method="post" enctype="multipart/form-data" action="{{route('timeslots.import',['id'=>$id])}}">
                                            @csrf
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>Select File for Upload</label>
                                                    <input type="file" name="select_file" class="form-control"><br>
                                                    <button type="submit" class="btn btn-primary">Upload</button>
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
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>Grade 1</th>
                                        <th>Grade 2</th>
                                        <th>Grade 3</th>
                                        <th>Grade 4</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($timeslots as $timeslot)
                                        <tr>
                                            <td>{{$timeslot->date}}</td>
                                            <td>{{$timeslot->start_time}}</td>
                                            <td>{{$timeslot->grade_1}}</td>
                                            <td>{{$timeslot->grade_2}}</td>
                                            <td>{{$timeslot->grade_3}}</td>
                                            <td>{{$timeslot->grade_4}}</td>
                                            <td>
                                                <a href="{{route('clinic.timeslots.delete',['id'=>$timeslot->id])}}" class="btn btn-danger">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        {{$timeslots->links()}}
                        <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
    </div>
    <!-- ./wrapper -->
@endsection

