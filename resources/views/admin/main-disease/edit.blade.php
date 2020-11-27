@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Disease</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Disease</li>
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
                                <h3 class="card-title">Disease Edit</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('main-disease.update',['id'=>$disease->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Name</label>
                                                <input type="text" class="form-control" id="exampleInputEmail1" name="name" placeholder="Enter Name" value="{{$disease->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select class="form-control" name="isactive">
                                                    <option value="1" {{$disease->isactive==1?'selected':''}}>Yes</option>
                                                    <option value="0" {{$disease->isactive==0?'selected':''}}>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body">
                            <div class="row">
                    <a href="{{route('main-disease.treatment-add', ['id'=>$disease->id])}}" class="btn btn-primary">+ Add New Treatment</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <br>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Treatments List</h3>
                            </div>

                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        @foreach($treatments as $t)
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">
                                                    <b>{{$loop->iteration}}.</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    {{$t->description}}
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                              <a href="" class="btn btn-primary">Edit</a>
                                                </h3>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                <dl>
                                                    <dt>Other Diseases</dt>
                                                    <dd>
                                                        @foreach($t->reasonDiseases as $d) {{$d->name}},
                                                        @endforeach
                                                    </dd>
                                                    <dt>Pain Points</dt>
                                                    <dd>
                                                        @foreach($t->painPoints as $p) {{$p->name}},
                                                        @endforeach
                                                    </dd>

                                                    <dt>Avoid with below diseases</dt>
                                                    <dd>
                                                        @foreach($t->ignoreWhenDiseases as $d) {{$d->name}},
                                                        @endforeach
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
                                            <!-- /.card-body -->
                                        </div>
                                        @endforeach
                                        <!-- /.card -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>
    <!-- ./wrapper -->
@endsection

