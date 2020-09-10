@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Order</h1>
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
                           <input  id="fullname"  class="form-control" name="search" placeholder=" search name" value="{{request('search')}}"  type="text" />
                           </div>
					  <div class="col-4">

						  <select id="status" name="status" class="form-control" >

                              <option value="" {{ request('status')==''?'selected':''}}>Please select</option>
                             <option value="pending" {{ request('status')=='pending'?'selected':''}}>pending</option>
                             <option value="confirmed" {{ request('status')==='confirmed'?'selected':''}}>confirmed</option>
                             <option value="cancelled" {{ request('status')=='cancelled'?'selected':''}}>cancelled</option>
                          </select>

                      </div>
                      <div class="col-4">
                          <select id="payment_status" name="payment_status" class="form-control" >

							 <option value="" {{ request('payment_status')==''?'selected':''}}>Please Select</option>
							 <option value="paid" {{ request('payment_status')=='paid'?'selected':''}}>paid</option>
                             <option value="payment-wait" {{ request('payment_status')==='payment-wait'?'selected':''}}>payment-wait</option>

                          </select>

                      </div><br><br>
                      <div class="col-4">
                           <input  id="fullname"  class="form-control" name="fromdate" placeholder=" search name" value="{{request('fromdate')}}"  type="date" />
                           </div>
                           <div class="col-4">
                           <input  id="fullname"  class="form-control" name="todate" placeholder=" search name" value="{{request('todate')}}"  type="date" />
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
                                        <th>OrderID</th>
                                        <th>User</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Payment Mode</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>{{$order->refid}}</td>
                                            <td>{{$order->customer->name??''}} <br>Mob: {{$order->customer->mobile??''}}</td>
                                            <td>{{$order->created_at}}</td>
                                            <td>
                                                @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)
                                                Therapy Booking
                                                @else
                                                Product Purchase
                                                @endif
                                            </td>
                                            <td>{{$order->total_cost}}</td>
                                            <td>{{config('arogyapeeth.product_status')[$order->status]}}</td>
                                            <td>{{$order->payment_status=='paid'?'Paid':'Pending'}}</td>
{{--                                            <td>{{$order->payment_mode}}</td>--}}
                                            <td><a href="{{route('order.productdetails',['id'=>$order->id])}}" class="btn btn-success">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>OrderID</th>
                                        <th>User</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Cost</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Payment Mode</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        {{$orders->links()}}
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
