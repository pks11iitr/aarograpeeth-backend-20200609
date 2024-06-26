@extends('layouts.admin')
@section('content')
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
                            <li class="breadcrumb-item active"><a href="{{route('orders.list')}}">Order Details</a></li>
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

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Order Details</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Order ID</td>
                                        <td>{{$order->refid}}</td>
                                    </tr>
                                    <tr>
                                        <td>Date & Time</td>
                                        <td>{{$order->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>{{$order->total_cost}}</td>
                                    </tr>
                                    <tr>
                                        <td>Payment Status</td>
                                        <td>{{$order->payment_status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{config('arogyapeeth.product_status')[$order->status]}}<br><br>
                                            @if(in_array($order->status, ['confirmed']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id,'status'=>'processing'])}}" name='status' class="btn btn-primary">Processing</a>
                                            @endif
                                            @if(in_array($order->status, ['processing']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id,'status'=>'dispatched'])}}" name='status' class="btn btn-primary">Dispatched</a>
                                            @endif
                                            @if(in_array($order->status, ['dispatched']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id,'status'=>'delivered'])}}" name='status' class="btn btn-primary">Delivered</a><br><br>
                                            @endif
                                            @if(in_array($order->status, ['confirmed', 'pending', 'processing']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id,'status'=>'cancelled'])}}" name='status' class="btn btn-primary">Cancel</a>
                                            @endif
                                            @if(in_array($order->status, ['return-request']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id,'status'=>'return-accepted'])}}" name='status' class="btn btn-primary">Accept Return Request</a>
                                            @endif
                                            @if($order->payment_status=='paid' && in_array($order->status, ['return-accepted', 'cancelled']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id, 'status'=>'refunded'])}}" name='status' class="btn btn-primary">Refund</a>
                                            @endif
                                            @if(in_array($order->status, ['delivered']))
                                                <a href="{{route('orders.status.change', ['id'=>$order->id,'status'=>'completed'])}}" name='status' class="btn btn-primary">Completed</a>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>@if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy) Therapy Details <th></th> @else Product Details @endif </th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)
                                        @foreach($order->details as $detail)
                                            <tr>
                                                <td>{{$detail->entity->name??''}}</td>
                                                <td>Grade {{$detail->grade??''}}</td>
                                                <td>Sessions: {{$detail->quantity}}</td>

                                                <td>Rs. {{$detail->cost}}/session</td>
                                                <td>Rs. {{$detail->cost*$detail->quantity}} Total</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach($order->details as $detail)
                                            <tr>
                                                <td>{{$detail->entity->name??''}}</td>
                                                <td>Quantity: {{$detail->quantity}}</td>
                                                <td>Rs. {{$detail->cost}}/Item</td>
                                                <td>Rs. {{$detail->cost*$detail->quantity}} Total</td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Customer Details</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td>{{$order->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Mobile</td>
                                        <td>{{$order->mobile}}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{$order->email}}</td>
                                    </tr>
                                    <tr>
                                        <td>Address</td>
                                        <td>{{$order->adderss}}</td>
                                    </tr>
                                    @if(!empty($order->details[0]->entity) && $order->details[0]->entity instanceof \App\Models\Therapy)
                                        <tr>
                                            <td>Booking Date</td>
                                            <td>{{$order->booking_date}}</td>
                                        </tr>
                                        <tr>
                                            <td>Booking Time</td>
                                            <td>{{$order->booking_time}}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        @if($order->details[0]->entity_type=='App\Models\Therapy')
                            <!-- /.card-body -->
                                <div class="card-body">
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Date Sessions</th>
                                            <th>Time Sessions</th>
                                            <th>Status Sessions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($order->details[0]->clinic_id) )

                                            @foreach($order->bookingSlots as $bookingSlot)
                                                <tr>
                                                    <td>{{$bookingSlot->timeslot->date??''}}</td>
                                                    <td>{{$bookingSlot->timeslot->start_time??''}}</td>
                                                    <td>{{$bookingSlot->status}}</td>
                                                </tr>
                                            @endforeach

                                        @else
                                            @foreach($order->homebookingslots as $homebookingslot)
                                                <tr>
                                                    <td>{{$homebookingslot->timeslot->date??''}}</td>
                                                    <td>{{$homebookingslot->timeslot->start_time??''}}</td>
                                                    <td>{{$homebookingslot->status}}</td>
                                                </tr>
                                            @endforeach

                                        @endif
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                        @endif
                        <!-- /.card -->
                        </div>
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
