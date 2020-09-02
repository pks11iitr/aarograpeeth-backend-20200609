@extends('layouts.clinicadmin')
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
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('therapist.update',['id'=>$therapist->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputtitle">Clinic Name</label>
                                        <select name="clinic_id" class="form-control" id="exampleInputistop" placeholder="">
                                            @foreach($clinic as $c)
                                                <option value="{{$c->id}}" {{$therapist->clinic_id==$c->id?'selected':''}}>{{$c->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Name</label>
                                        <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$therapist->name}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Email</label>
                                        <input type="text" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Email" value="{{$therapist->email}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Mobile</label>
                                        <input type="text" name="mobile" class="form-control" id="exampleInputEmail1" placeholder="Enter mobile" value="{{$therapist->mobile}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Password</label>
                                        <input type="text" name="password" class="form-control" id="exampleInputEmail1" placeholder="Enter password" value="{{$therapist->password}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Address</label>
                                        <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter address" value="{{$therapist->address}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">City</label>
                                        <input type="text"name="city" class="form-control" id="exampleInputEmail1" placeholder="Enter city" value="{{$therapist->city}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">State</label>
                                        <input type="text"name="state" class="form-control" id="exampleInputEmail1" placeholder="Enter state" value="{{$therapist->state}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Is Active</label>
                                        <select class="form-control" name="isactive" required>
                                            <option value="1" {{$therapist->isactive==1?'selected':''}} >Yes</option>
                                            <option value="0" {{$therapist->isactive==0?'selected':''}} >No</option>
                                        </select>
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
                                    <image src="{{$therapist->image}}" height="100" width="200">
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

