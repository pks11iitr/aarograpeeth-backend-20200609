@extends('layouts.clinicadmin')
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
                                        <td>Status</td>
                                        <td>{{$order->status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Payment Status</td>
                                        <td>{{$order->payment_status}}</td>
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
                                        <th>Therapy Details</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->details as $detail)
                                            <tr>
                                                <td>{{$detail->entity->name??''}}</td>
                                                <td>Grade {{$detail->grade??''}}</td>
                                                <td>Sessions: {{$detail->quantity}}</td>

                                                <td>Rs. {{$detail->cost}}/session</td>
                                                <td>Rs. {{$detail->cost*$detail->quantity}} Total</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- /.card-body -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Date Sessions</th>
                                        <th>Time Sessions</th>
                                        <th>Status Sessions</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($order->details[0]->clinic_id) )

                                        @foreach($order->bookingSlots as $bookingSlot)
                                            <tr>
                                                <td>{{$bookingSlot->timeslot->date}}</td>
                                                <td>{{$bookingSlot->timeslot->start_time}}</td>
                                                <td>{{$bookingSlot->status}}</td>
                                                <td><a href="{{route('order.edit',['id'=>$order->id])}}" class="btn btn-success">Edit</a></td>
                                            </tr>
                                        @endforeach

                                    @else
                                        @foreach($order->homebookingslots as $homebookingslot)
                                            <tr>
                                                <td>{{$homebookingslot->timeslot->date}}</td>
                                                <td>{{$homebookingslot->timeslot->start_time}}</td>
                                                <td>{{$homebookingslot->status}}</td>
                                            </tr>
                                        @endforeach

                                    @endif
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>

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
