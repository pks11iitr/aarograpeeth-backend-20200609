@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Clinic Update</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Clinic Update</li>
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
                <h3 class="card-title">Clinic Update</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('clinic.update',['id'=>$clinic->id])}}">
                 @csrf
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" value="{{$clinic->name}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Description</label><br>
                    <textarea id="w3review" name="description" rows="4" cols="120">{{$clinic->description}} </textarea>
                   <!-- <input type="text" name="description" class="form-control" id="exampleInputEmail1" placeholder="Enter Description" value="{{$clinic->description}}"> -->
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Address</label>
                    <input type="text" name="address" class="form-control" id="exampleInputEmail1" placeholder="Enter Address" value="{{$clinic->address}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">City</label>
                    <input type="text" name="city" class="form-control" id="exampleInputEmail1" placeholder="Enter City" value="{{$clinic->city}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">State</label>
                    <input type="text" name="state" class="form-control" id="exampleInputEmail1" placeholder="Enter State" value="{{$clinic->state}}">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Contact</label>
                    <input type="text"name="contact" class="form-control" id="exampleInputEmail1" placeholder="Enter Contact" value="{{$clinic->contact}}">
                  </div>
{{--                  <div class="form-group">--}}
{{--                    <label for="exampleInputEmail1">Lat</label>--}}
{{--                    <input type="text"name="lat" class="form-control" id="exampleInputEmail1" placeholder="Enter Lat" value="{{$clinic->lat}}">--}}
{{--                  </div>--}}
{{--                  <div class="form-group">--}}
{{--                    <label for="exampleInputEmail1">Lang</label>--}}
{{--                    <input type="text"name="lang" class="form-control" id="exampleInputEmail1" placeholder="Enter Lang" value="{{$clinic->lang}}">--}}
{{--                  </div>--}}
                    <div class="form-group">
                        <label>Is Active</label>
                        <select class="form-control" name="isactive" required>
                           <option  selected="selected" value="1" {{$clinic->isactive==1?'selected':''}}>Yes</option>
                            <option value="0" {{$clinic->isactive==0?'selected':''}}>No</option>
                        </select>
                      </div>
                  <div class="form-group">
                    <label for="exampleInputFile">Thumbnail Image</label>
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
                  <image src="{{$clinic->image}}" height="100" width="200">
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
     <!--*******************************************************************************************************************-->
            <section class="content">
            <div class="container-fluid">
                <!-- SELECT2 EXAMPLE -->
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title">Add Images</h3>
                    </div>
                    <!-- /.card-header -->
                    <form action="{{route('clinic.document',['id'=>$clinic->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                            <div class="card-body">
                            <!-- /.row -->
                            <div class="row">
                                <!-- /.col -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Header Image</label>
                                        <input type="file" class="form-control" name="file_path[]" id="exampleInputEmail1" placeholder="Select image" multiple>
                                        <br>
                                    </div>
                                    <!-- /.form-group -->
                                 </div>
                                <!-- /.col -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">.</label>
                                        <button type="submit" class="btn btn-block btn-primary btn-sm">Add</button>
                                    </div>
                                    <!-- /.form-group -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="row">
                                <!-- /.col -->
                                @foreach($documents as $document)
                                 <div class="form-group">
                                        <img src="{{$document->file_path}}" height="100" width="200"> &nbsp; &nbsp; <a href="{{route('clinic.delete',['id'=>$document->id])}}">X</a>
                                        &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;          &nbsp; &nbsp; &nbsp; &nbsp;
                                  </div>
                               @endforeach
                                 <!-- /.form-group -->
                                    <!-- /.form-group -->
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                    </form>
                </div>



            </div>
        </section>
  <!--**********************************************************************************************************************-->

      <section class="content">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-12">
                      <div class="card card-primary">
                          <div class="card-header">
                              Therapies Provided By {{$clinic->name}}

                          </div>
                          <!-- /.card-header -->
                          <div class="card-body">
                              <table id="example2" class="table table-bordered table-hover">
                                  <thead>
                                  <tr>
                                      <th>Threapy Name</th>
                                      <th>Grade 1</th>
                                      <th>Grade 2</th>
                                      <th>Grade 3</th>
                                      <th>Grade 4</th>
                                      <th>Isactive</th>
                                      <th>Action</th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach($clinictherapys as $clinictherapy)
                                      <tr>
                                          <td>{{$clinictherapy->therapy->name}}</td>
                                          <td>{{'Price: '.$clinictherapy->grade1_price}} @if(!empty($clinictherapy->grade1_original_price))<br> {{'Old Price: '.$clinictherapy->grade1_original_price}}@endif </td>
                                          <td>{{'Price: '.$clinictherapy->grade2_price}} @if(!empty($clinictherapy->grade2_original_price))<br> {{'Old Price: '.$clinictherapy->grade2_original_price}}@endif </td>
                                          <td>{{'Price: '.$clinictherapy->grade3_price}} @if(!empty($clinictherapy->grade3_original_price))<br> {{'Old Price: '.$clinictherapy->grade3_original_price}}@endif </td>
                                          <td>{{'Price: '.$clinictherapy->grade4_price}} @if(!empty($clinictherapy->grade4_original_price))<br> {{'Old Price: '.$clinictherapy->grade4_original_price}}@endif </td>
                                          <td>
                                              @if($clinictherapy->isactive==1){{'Yes'}}
                                              @else{{'No'}}
                                              @endif
                                          </td>
{{--                                          <td><a href="{{route('clinic.therapyedelete',['id'=>$clinictherapy->id])}}" class="btn btn-success">Delete</a><br><br>--}}
                                          <td><a href="{{route('clinic.therapyedit',['id'=>$clinictherapy->id])}}" class="btn btn-success">Edit</a>
                                          </td>
                                      </tr>
                                  @endforeach
                                  </tbody>
                                  <tfoot>
                                  <tr>
                                      <th>Threapy Name</th>
                                      <th>Grade 1 </th>
                                      <th>Grade 2 </th>
                                      <th>Grade 3</th>
                                      <th>Grade 4</th>
                                      <th>Isactive</th>
                                      <th>Action</th>
                                  </tr>
                                  </tfoot>
                              </table>
                          </div>
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
              <form role="form" method="post" enctype="multipart/form-data" action="{{route('clinic.therapystore',['id'=>$clinic->id])}}">
                 @csrf
                <div class="card-body">
		       <div class="row">
				<div class="col-md-12">
                    <div class="form-group">
                        <label>Therapy Name</label>
                        <select name="therapy_id" class="form-control" id="exampleInputistop" placeholder="">
                                  @foreach($therapys as $therapy)
                                    <option value="{{$therapy->id}}">{{$therapy->name}} </option>
                                   @endforeach
                             </select>
                      </div>
                    </div>
				<div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade1 Price</label>
                    <input type="text" name="grade1_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                  </div>
                </div>
                 <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade1 Old Price</label>
                     <input type="text" name="grade1_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                     </div>
                 </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade2 Price</label>
                    <input type="text" name="grade2_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade2 Old Price</label>
                     <input type="text" name="grade2_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                     </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade3 Price</label>
                    <input type="text" name="grade3_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                     </div>
                </div>
               <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade3 Old Price</label>
                    <input type="text" name="grade3_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                     </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade4 Price</label>
                   <input type="text" name="grade4_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Grade4 Old Price</label>
                    <input type="text" name="grade4_original_price" class="form-control" id="exampleInputEmail1" placeholder="Enter price" >
                     </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Is Active</label>
                        <select name="isactive" class="form-control" id="exampleInputistop" placeholder="">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
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

