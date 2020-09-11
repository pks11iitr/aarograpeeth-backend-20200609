@extends('layouts.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Therapy</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">DataTables</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
				          <div class="row">
						 <div class="col-3">
                         <a href="{{route('therapy.create')}}" class="btn btn-primary">Add Therapy</a> </div>
                          <div class="col-9">

        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">

                     <div class="row">
					      <div class="col-4">
                           <input  id="fullname"  class="form-control" name="search" placeholder=" search name" value="{{request('search')}}"  type="text" />
                           </div>
					  <div class="col-4">
                          <select id="ordertype" name="ordertype" class="form-control" >
                              <option value="" {{ request('ordertype')==''?'selected':''}}>Please Select</option>
                             <option value="DESC" {{ request('ordertype')=='DESC'?'selected':''}}>DESC</option>
                              <option value="ASC" {{ request('ordertype')=='ASC'?'selected':''}}>ASC</option>
                          </select>
                      </div>
                    <div class="col-4">
                       <button type="submit" name="save" class="btn btn-primary">Submit</button>
                     </div>
                  </div>
              </form>
         </div>

     </div>
  </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <!--<th>Description</th>-->
                    <th>Grade 1 Price</th>
                    <th>Grade 2 Price</th>
                    <th>Grade 3 Price</th>
                    <th>Grade 4 Price</th>
                    <th>Image</th>
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				@foreach($therapist as $therapy)
                  <tr>
                      <td>{{$therapy->name}}<br><span style="color:darkblue">Reviews:{{$therapy->reviews()->count()}}</span><br><span style="color:green">Reviews:{{number_format($therapy->reviews()->avg('rating'),1)}}</span></td>
					  <!--<td>{{$therapy->description}}</td>-->
					  <td>{{$therapy->grade1_price}}</td>
					  <td>{{$therapy->grade2_price}}</td>
					  <td>{{$therapy->grade3_price}}</td>
					  <td>{{$therapy->grade4_price}}</td>
                      <td><img src="{{$therapy->image}}" height="80px" width="80px"/></td>
                       <td>
                        @if($therapy->isactive==1){{'Yes'}}
                             @else{{'No'}}
                             @endif
                        </td>
                      <td><a href="{{route('therapy.edit',['id'=>$therapy->id])}}" class="btn btn-success">Edit</a></td>
                  <!--    <a href="{{route('banners.delete',['id'=>$therapy->id])}}" class="btn btn-success">Delete</a></td>-->
                 </tr>
                 @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Name</th>
                   <!-- <th>Description</th>-->
                    <th>Grade First</th>
                    <th>Grade Second</th>
                    <th>Grade Third</th>
                    <th>Grade Fourth</th>
                    <th>Image</th>
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
               {{$therapist->links()}}
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
    <!-- /.content -->

  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
@endsection

