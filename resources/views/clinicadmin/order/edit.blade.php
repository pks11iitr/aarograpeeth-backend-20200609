@extends('layouts.clinicadmin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Sessions Add</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Sessions</li>
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
                                <h3 class="card-title">Sessions Add</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('order.store',['id'=>$order->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputtitle">Therapist Name</label>
                                        <select name="therapist_id" class="form-control" id="exampleInputistop" placeholder="">
                                            @foreach($therapists as $therapist)
                                                <option value="{{$therapist->id}}" {{$order->therapy_id==$therapist->id?'selected':''}}>{{$therapist->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="cancelled
" {{$order->status=='cancelled'?'selected':''}} >cancelled</option>
                                            <option value="pending" {{$order->status=='pending'?'selected':''}} >pending</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <input type="hidden" id="home_id" name="home_id" value="{{$order->id}}">
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

