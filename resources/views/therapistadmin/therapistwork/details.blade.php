@extends('layouts.therapistadmin')
@section('stylesheets')
    <style>
        .rating {
            display: inline-block;
            position: relative;
            height: 50px;
            line-height: 50px;
            font-size: 50px;
        }

        .rating label {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            cursor: pointer;
        }

        .rating label:last-child {
            position: static;
        }

        .rating label:nth-child(1) {
            z-index: 5;
        }

        .rating label:nth-child(2) {
            z-index: 4;
        }

        .rating label:nth-child(3) {
            z-index: 3;
        }

        .rating label:nth-child(4) {
            z-index: 2;
        }

        .rating label:nth-child(5) {
            z-index: 1;
        }

        .rating label input {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .rating label .icon {
            float: left;
            color: transparent;
        }

        .rating label:last-child .icon {
            color: #000;
        }

        .rating:not(:hover) label input:checked ~ .icon,
        .rating:hover label:hover input ~ .icon {
            color: #09f;
        }

        .rating label input:focus:not(:checked) ~ .icon:last-child {
            color: #000;
            text-shadow: 0 0 5px #09f;
        }
    </style>
@endsection
@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapist Work</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('therapistwork.list')}}">Therapist Work</a></li>
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
                                <h3 class="card-title">Session Details</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Order ID</td>
                                        <td>{{$openbooking->order->refid??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Session ID</td>
                                        <td>SESSION{{$openbooking->id??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Date</td>
                                        <td>{{$openbooking->timeslot->date??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Time</td>
                                        <td>{{$openbooking->timeslot->start_time??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Therapy Name</td>
                                        <td>{{$openbooking->therapy->name??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{$openbooking->status}}</td>
                                    </tr>
                                    <tr>
                                        <td>Date & Time</td>
                                        <td>{{$openbooking->created_at}}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card -->
                        </div>


                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Diagnose</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <form action="{{route('therapistwork.diagnose', ['id'=>$openbooking->id])}}" method="post">
                                    @csrf
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                <h3>Pain Point:</h3>
                                @foreach($painpoints as $painpoint)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="pain_point_ids[]" value="{{$painpoint->id}}" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id){{'checked'}} @endif @endforeach>
                                        <label class="form-check-label" for="exampleCheck1">{{$painpoint->name}}</label>
                                    </div>
                                @endforeach
                                    </div>
                                </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                <!-- One "tab" for each step in the form: -->
                                <h3>Disease it Any:</h3>
                                @foreach($diseases as $disease)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck2" name="disease_ids[]" value="{{$disease->id}}"  @foreach($selected_diseases as $s) @if($s->disease_id==$disease->id){{'checked'}} @endif @endforeach >
                                        <label class="form-check-label" for="exampleCheck2">{{$disease->name}}</label>
                                    </div>
                                @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>

                                </div>
                                </form>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Select Treatment</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <form action="{{route('therapistwork.start', ['id'=>$openbooking->id])}}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <h3>Treatments</h3>
                                                @foreach($treatments as $treatment)
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" id="exampleCheck1" name="treatment_id" value="{{$treatment->id}}" @if($treatment->id==$openbooking->treatment_id){{'checked'}}@endif>
                                                        <label class="form-check-label" for="exampleCheck1">{{$treatment->name}}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="submit" class="btn btn-primary">Start Therapy</button>
                                        </div>

                                    </div>
                                </form>
                            </div>

                        </div>


                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Complete Response</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <form action="{{route('therapistwork.feedback', ['id'=>$openbooking->id])}}" method="post" class="">
                                    @csrf
                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                <h3>Pain Point:</h3>
                                @foreach($painpoints as $painpoint)
                                    <div class="form-check">
                                        <label class="form-check-label" for="exampleCheck1">{{$painpoint->name}}</label>
                                        <label>
                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="1" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==1){{'checked'}} @endif @endforeach/>
                                            <span class="icon">★</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="2" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==2){{'checked'}} @endif @endforeach/>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="3" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==3){{'checked'}} @endif @endforeach/>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="4" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==4){{'checked'}} @endif @endforeach/>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                        </label>
                                        <label>
                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="5" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==5){{'checked'}} @endif @endforeach/>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                            <span class="icon">★</span>
                                        </label>

                                    </div>
                                @endforeach
                                    </div>
                                </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Customer Comments</label>
                                            <textarea class="form-control" name="comments" required>{{$openbooking->message}}</textarea>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Complete Therapy</button>
                                        </div>
                                    </div>


                                </form>
                            </div>

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
        <!-- ./wrapper -->
    </div>
    <!-- ./wrapper -->

@endsection
@section('scripts')
    <script>
    $(':radio').change(function() {
    console.log('New star rating: ' + this.value);
    });
    </script>
@endsection
