@extends('layouts.clinicadmin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Timeslots</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Timeslots Add</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Timeslot Add</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('clinic.timeslots.add')}}">
                                @csrf
                                <div class="card-body">
                                    @for($i=0; $i<10;$i++)
                                    <div class="row">
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Date</label>
                                            <input type="date" name="date[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="">
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Time</label>
                                            <input type="time" name="time[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="">
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Duration</label>
                                                <input type="text" name="duration[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="Enter Minutes">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Grade 1 Count</label>
                                            <input type="text" name="grade_1[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="Enter Number">
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Grade 2 Count</label>
                                            <input type="text" name="grade_2[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="Enter Number">
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Grade 3 Count</label>
                                            <input type="text" name="grade_3[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="Enter Number">
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Grade 4 Count</label>
                                            <input type="text"name="grade_4[{{$i}}]" class="form-control" id="exampleInputEmail1" placeholder="Enter Number">
                                        </div>
                                        </div>
                                    </div>
                                    @endfor
                                    <hr>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
@endsection

