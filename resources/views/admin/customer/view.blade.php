@extends('layouts.admin')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Customers</h1>
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
                          <div class="col-12">

        <form class="form-validate form-horizontal"  method="get" action="" enctype="multipart/form-data">
                     <div class="row">
					      <div class="col-4">
                           <input  class="form-control" name="search" placeholder=" search name" value="{{request('search')}}"  type="text" />
                           </div>
					  <div class="col-4">
                          <select id="ordertype" name="ordertype" class="form-control" >
                              <option value="" {{ request('ordertype')==''?'selected':''}}>Please Select</option>
                             <option value="DESC" {{ request('ordertype')=='DESC'?'selected':''}}>DESC</option>
                              <option value="ASC" {{ request('ordertype')=='ASC'?'selected':''}}>ASC</option>
                          </select>
                      </div>
                      <div class="col-4">
                          <select id="status" name="status" class="form-control" >

                             <option value="">Please Select Status</option>

                             <option value="1" {{ request('status')=='1'?'selected':''}}>Active</option>
                             <option value="0" {{ request('status')==='0'?'selected':''}}>Inactive</option>
                             <option value="2" {{ request('status')=='2'?'selected':''}}>Blocked</option>
                          </select>
                      </div><br><br>
                      <div class="col-4">
                           <input   class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                           </div>
                           <div class="col-4">
                           <input  class="form-control" name="todate" placeholder=" search name" value="{{request('todate')}}"  type="date" />
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
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>DOB</th>
                  <!--  <th>Address</th>
                    <th>City</th>
                    <th>State</th>-->
                    <th>Image</th>
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				@foreach($customers as $customer)
                  <tr>
					  <td>{{$customer->name}}</td>
					  <td>{{$customer->mobile}}</td>
					  <td>{{$customer->email}}</td>
					  <td>{{$customer->dob}}</td>
					 <!-- <td>{{$customer->address}}</td>
					  <td>{{$customer->city}}</td>
					  <td>{{$customer->state}}</td>-->
                      <td><img src="{{$customer->image}}" height="80px" width="80px"/></td>
                       <td>
                        @if($customer->status==1){{'Active'}}
                             @elseif($customer->status==2){{'Blocked'}}@else{{'Inactive'}}
                             @endif
                        </td>
                      <td><a href="{{route('customer.edit',['id'=>$customer->id])}}" class="btn btn-success">Edit</a></br></br>
                      <a href="{{route('customer.edit',['id'=>$customer->id])}}" class="open-AddBookDialog btn btn-success" data-toggle="modal" data-target="#exampleModal" data-id="{{$customer->id}}">Notification</a></td>
                 </tr>
                 @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>DOB</th>
                   <!-- <th>Address</th>
                    <th>City</th>
                    <th>State</th>-->
                    <th>Image</th>
                    <th>Isactive</th>
                   <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              {{$customers->appends(request()->query())->links()}}
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send Notification</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" enctype="multipart/form-data" onsubmit=" return verifySubmit()" >
			 @csrf
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Title:</label>
            <input type="text" name="title" class="form-control" id="recipient-name" required>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" name="message" id="message-text" required></textarea>
          </div>
          <input type="hidden" name="cusid" class="form-control" id="cusid">
           <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
             <button class="btn btn-primary" type="submit">Send message</button>
           </div>
        </form>
      </div>

    </div>
  </div>
</div>
<script>

$(document).on("click", ".open-AddBookDialog", function () {
     var myBookId = $(this).data('id');
     $(".modal-body #cusid").val( myBookId );
     // As pointed out in comments,
     // it is unnecessary to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});
function verifySubmit(){

	var cusid = $("#cusid").val();
	var title = $("#recipient-name").val();
	var des = $("#message-text").val();

	$.post('{{route('customer.send_message')}}', {cusid:cusid, _token:'{{csrf_token()}}', title:title, des:des}, function(data){
					alert('Message has been sent successfully')
			})

             window.location.reload();
           // console.log(data);

	}
 </script>
      <!-- /.container-fluid -->
    </section>
    </div>
    <!-- /.content -->

  <!-- /.control-sidebar -->

<!-- ./wrapper -->
@endsection

