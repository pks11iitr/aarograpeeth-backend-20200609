@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapist Edit</h1>
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
                                <h3 class="card-title">Therapist Edit</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('therapists.update',['id'=>$therapist->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$therapist->name}}" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Mobile</label>
                                                <input type="text" name="mobile" class="form-control" id="exampleInputEmail1" placeholder="Enter mobile" value="{{$therapist->mobile}}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">City</label>
                                                <input type="text"name="city" class="form-control" id="exampleInputEmail1" placeholder="Enter city" value="{{$therapist->city}}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">State</label>
                                                <input type="text"name="state" class="form-control" id="exampleInputEmail1" placeholder="Enter state" value="{{$therapist->state}}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select class="form-control" name="isactive" required>
                                                    <option value="1" {{$therapist->isactive==1?'selected':''}} >Yes</option>
                                                    <option value="0" {{$therapist->isactive==0?'selected':''}} >No</option>
                                                </select>
                                            </div>

                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email</label>
                                                    <input type="text" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Email" value="{{$therapist->email}}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Password (Leave Blank If Unchanged)</label>
                                                    <input type="text" name="password" class="form-control" id="exampleInputEmail1" placeholder="Enter password" value="">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Address</label>
                                                    <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter address" value="{{$therapist->address}}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputFile">File input</label>
                                                    <div class="input-group">
                                                        <div class="custom-file">
                                                            <input type="file" name="image" class="custom-file-input" id="exampleInputFile" accept="image/*">
                                                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                                        </div>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" id="">Upload</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <image src="{{$therapist->image}}" height="100" width="200"/>
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
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Add Therapy</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('therapists.therapystore',['id'=>$therapist->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Therapy Name</label>
                                                <select name="therapy_id" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="">Please Select Therapy</option>
                                                    @foreach($therapys as $therapy)
                                                        <option value="{{$therapy->id}}">{{$therapy->name}} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Therapist Grade</label>
                                                <select name="therapist_grade" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="">Please Select Grade</option>
                                                    <option value="1">Grade 1</option>
                                                    <option value="2">Grade 2</option>
                                                    <option value="3">Grade 3</option>
                                                    <option value="4">Grade 4</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select name="isactive" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="">Please Select</option>
                                                    <option value="Applied">Applied</option>
                                                    <option value="Approved">Approved</option>
                                                    <option value="Rejected">Rejected</option>
                                                    <option value="Revoked">Revoked</option>
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
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>

        <!--**********************************************************************************************************************-->

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Therapies</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Threapy Name</th>
                                        <th>Grade </th>
                                        <th>Isactive</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($therapistherapys as $therapistherapy)
                                        <tr>
                                            <td>{{$therapistherapy->therapy->name??''}}</td>
                                            <td>{{$therapistherapy->therapist_grade??''}}</td>
                                            <td>{{$therapistherapy->isactive??''}}</td>
                                            <td><a href="{{route('therapists.therapyedit',['id'=>$therapistherapy->id??''])}}" class="btn btn-success">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        {{$therapistherapys->links()}}
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
        <!--****************************************************************************************************************-->
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
@endsection

