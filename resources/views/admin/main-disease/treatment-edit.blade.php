@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Treatment for {{$disease->name}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{route('main-disease.edit', ['id'=>$disease->id])}}">{{$disease->name}}</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('main-disease.edit', ['id'=>$disease->id])}}">Disease</a></li>
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
                                <h3 class="card-title">Fill Treatment Details</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="post" enctype="multipart/form-data" action="{{route('main-disease.treatment-edit', ['id'=>$disease->id, 'treatment_id'=>$treatment->id])}}">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Title</label>
                                                <textarea class="form-control" id="exampleInputEmail1" name="title" placeholder="" required rows="2">{{$treatment->title}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Formulae</label>
                                                <textarea class="form-control" id="exampleInputEmail1" name="description" placeholder="" required rows="4">{{$treatment->description}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Precautions</label>
                                                <textarea class="form-control" id="exampleInputEmail1" name="precautions" placeholder="" rows="4">{{$treatment->precautions}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Exercise</label>
                                                <textarea class="form-control" id="exampleInputEmail1" name="exercise" placeholder="" rows="2">{{$treatment->exercise}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Diet</label>
                                                <textarea class="form-control" id="exampleInputEmail1" name="diet" placeholder="" rows="2">{{$treatment->diet}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pain Points</label>
                                                <select class="form-control" name="pain_points[]" multiple>
                                                    @foreach($pain_points as $p)
                                                        <option value="{{$p->id}}" @foreach($treatment->painPoints as $pp) @if($pp->id==$p->id){{'selected'}} @endif @endforeach>{{$p->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Is Active</label>
                                                <select class="form-control" name="isactive" required>
                                                    <option value="1" @if($treatment->isactive==1){{'selected'}}@endif>Yes</option>
                                                    <option value="0" @if($treatment->isactive==0){{'selected'}}@endif>No</option>
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
        <!-- /.content -->
    </div>
    <!-- ./wrapper -->
@endsection

