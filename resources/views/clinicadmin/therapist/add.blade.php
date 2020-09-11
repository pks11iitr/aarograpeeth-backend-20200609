@extends('layouts.clinicadmin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapist Add</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Therapist</li>
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
                                <h3 class="card-title">Therapist Add</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('clinicadmin.therapist.store')}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Mobile</label>
                                                <input type="text" name="mobile" class="form-control" id="exampleInputEmail1" placeholder="Enter mobile" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">City</label>
                                                <input type="text"name="city" class="form-control" id="exampleInputEmail1" placeholder="Enter city" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">State</label>
                                                <input type="text"name="state" class="form-control" id="exampleInputEmail1" placeholder="Enter state" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select class="form-control" name="isactive" required>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email</label>
                                                <input type="text" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Password</label>
                                                <input type="text" name="password" class="form-control" id="exampleInputEmail1" placeholder="Enter password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Address</label>
                                                <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter address" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputFile">File input</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" name="image" class="custom-file-input" id="exampleInputFile" accept="image/*" required>
                                                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="">Upload</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

