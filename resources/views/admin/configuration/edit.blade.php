@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Configuration Edit</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Configuration</li>
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
                <h3 class="card-title">COnfiguration Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('config.edit',['id'=>$configuration->id])}}">
                 @csrf
                <div class="card-body">
                <!--  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                  </div>-->
                   <div class="row">
                       <div class="form-group">
                        <label for="exampleInputFile">{{ucwords(str_replace('_', ' ', $configuration->param))}}</label>
                        </div>
                      </div>
                      @if(in_array($configuration->param, ['channel_url']))
                        <div class="form-group">


                                    <input type="text" name="param_value" class="form-control" value="{{$configuration->value}}">


                        </div>
                      @elseif(in_array($configuration->param, ['therapist_does_dont', 'customer_does_dont', 'what_to_do_if_pain_increase']))
                          <div class="form-group">
                                  <div class="custom-file">
                                      <textarea name="param_value" class="form-control"  rows="10">{{$configuration->value}}</textarea>
                                  </div>
                          </div>
                    @endif
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

