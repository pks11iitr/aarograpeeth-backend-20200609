@extends('layouts.admin')
@section('content')

    <!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" integrity="sha256-b5ZKCi55IX+24Jqn638cP/q3Nb2nlx+MH/vMMqrId6k=" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Review</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Review</li>
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
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Review</h3>

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Rating</th>
                                        <th>Description</th>
                                        <th>Name</th>
                                        <th>Review Type</th>
                                        <th>Isactive</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>{{$review->customer->name??''}}</td>
                                            <td>{{$review->rating}}</td>
                                            <td>{{$review->description}}</td>
                                        <!-- *****************************************************-->
                                            @if($review->entity_type=='App\Models\Clinic')
                                            <td>{{$review->clinic->name??''}}</td>
                                            @elseif($review->entity_type=='App\Models\Therapy')
                                                <td>{{$review->therapy->name??''}}</td>
                                            @elseif($review->entity_type=='App\Models\Product')
                                                <td>{{$review->product->name??''}}</td>
                                            @elseif($review->entity_type=='App\Models\User')
                                                <td>{{$review->user->name??''}}</td>
                                            @endif
                                        <!-- *****************************************************-->
                                            @if($review->entity_type=='App\Models\Clinic')
                                            <td>{{'Clinic'}}</td>
                                            @elseif($review->entity_type=='App\Models\Therapy')
                                                <td>{{'Therapy'}}</td>
                                            @elseif($review->entity_type=='App\Models\Product')
                                                <td>{{'Product'}}</td>
                                            @elseif($review->entity_type=='App\Models\User')
                                                <td>{{'User'}}</td>
                                            @endif
                                            <td>
                                                @if($review['isactive']=='1')
                                                    <a href="{{route('review.status',['id'=>$review['id']??'','isactive'=>0])}}" class="btn btn-success">Hide</a>
                                                @else
                                                    <a href="{{route('review.status',['id'=>$review['id']??'','isactive'=>1])}}" class="btn btn-danger">Show</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Rating</th>
                                        <th>Description</th>
                                        <th>Name</th>
                                        <th>Review Type</th>
                                        <th>Isactive</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        {{$reviews->links()}}
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

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script type="text/javascript">
            $(function () {
                $("#isactive").change(function () {
                    var isactive = $(this).val();
                    var id = $(this).data('id');

                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: '../status',
                        data: {'isactive': isactive, 'id': id},

                        success: function (data) {
                            console.log(data.success)
                        }
                    });

                });
            });
        </script>

        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
@endsection

