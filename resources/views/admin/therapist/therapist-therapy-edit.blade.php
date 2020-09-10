@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapist Therapy Update</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('therapists.edit', ['id'=>$therapisttherapy->therapist_id])}}">Back To Therapist</a></li>
                        </ol>
                    </div>
                </div>
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
                                <h3 class="card-title">Edit {{--{{$therapisttherapy->clinic->name}}--}} Therapy</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('therapists.therapyupdate',['id'=>$therapisttherapy->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Therapy Name</label>
                                                <select name="therapy_id" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="{{$therapisttherapy->therapy->id??''}}">{{$therapisttherapy->therapy->name??''}} </option>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Therapist Grade</label>
                                                <select name="therapist_grade" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="1" {{$therapisttherapy->therapist_grade==1?'selected':''}}>Grade 1</option>
                                                    <option value="2" {{$therapisttherapy->therapist_grade==2?'selected':''}}>Grade 2</option>
                                                    <option value="3" {{$therapisttherapy->therapist_grade==3?'selected':''}}>Grade 3</option>
                                                    <option value="4" {{$therapisttherapy->therapist_grade==4?'selected':''}}>Grade 4</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select name="isactive" class="form-control" id="exampleInputistop" placeholder="">
                                                    <option value="Applied" {{$therapisttherapy->isactive=='Applied'?'selected':''}}>Applied</option>
                                                    <option value="Approved" {{$therapisttherapy->isactive=='Approved'?'selected':''}}>Approved</option>
                                                    <option value="Rejected" {{$therapisttherapy->isactive=='Rejected'?'selected':''}}>Rejected</option>
                                                    <option value="Revoked" {{$therapisttherapy->isactive=='Revoked'?'selected':''}}>Revoked</option>
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
        <!--****************************************************************************************************************-->



    </div>
    <!-- ./wrapper -->
@endsection

