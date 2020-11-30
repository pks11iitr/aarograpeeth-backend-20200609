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
                        <h1>Therapy Sessions Details</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('therapistwork.list')}}">Back</a></li>
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
                                        <td>Patient Name</td>
                                        <td>{{$openbooking->order->name??''}}</td>
                                    </tr>
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


{{--                        <div class="card">--}}
{{--                            <div class="card-header">--}}
{{--                                <h3 class="card-title">Diagnose</h3>--}}
{{--                                <div class="card-tools">--}}
{{--                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- /.card-header -->--}}
{{--                            <!-- form start -->--}}

{{--                            <div class="card-body">--}}
{{--                                <form action="{{route('therapistwork.diagnose', ['id'=>$openbooking->id])}}" method="post">--}}
{{--                                    @csrf--}}
{{--                                <div class="row">--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                <h3>Pain Point:</h3>--}}
{{--                                @foreach($painpoints as $painpoint)--}}
{{--                                    <div class="form-check">--}}
{{--                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="pain_point_ids[]" value="{{$painpoint->id}}" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id){{'checked'}} @endif @endforeach>--}}
{{--                                        <label class="form-check-label" for="exampleCheck1">{{$painpoint->name}}</label>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                    <div class="col-md-6">--}}
{{--                                        <div class="form-group">--}}
{{--                                <!-- One "tab" for each step in the form: -->--}}
{{--                                <h3>Disease it Any:</h3>--}}
{{--                                @foreach($diseases as $disease)--}}
{{--                                    <div class="form-check">--}}
{{--                                        <input type="checkbox" class="form-check-input" id="exampleCheck2" name="disease_ids[]" value="{{$disease->id}}"  @foreach($selected_diseases as $s) @if($s->disease_id==$disease->id){{'checked'}} @endif @endforeach >--}}
{{--                                        <label class="form-check-label" for="exampleCheck2">{{$disease->name}}</label>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                                        </div>--}}
{{--                                        <button type="submit" class="btn btn-primary">Submit</button>--}}
{{--                                    </div>--}}

{{--                                </div>--}}
{{--                                </form>--}}
{{--                            </div>--}}

{{--                        </div>--}}

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Customer Diseases</h3>
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
                                                <h3>Main Diseases:</h3>
                                                @foreach($main_diseases as $md)
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="main_diseases[]" value="{{$md->id}}" @foreach($openbooking->mainDiseases as $smd) @if($smd->id==$md->id){{'checked'}} @endif @endforeach>
                                                        <label class="form-check-label" for="exampleCheck1">{{$md->name}}</label>
                                                    </div>
                                                    @foreach($reason_diseases as $rd)

                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" id="exampleCheck1" name="reason_diseases[{{$md->id}}][]" value="{{$rd->id}}" @foreach($openbooking->reasonDiseases as $srd) @if($srd->id==$rd->id && $srd->pivot->disease_id==$md->id){{'checked'}} @endif @endforeach>
                                                            <label class="form-check-label" for="exampleCheck1">{{$rd->name}}</label>
                                                        </div>

                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h3>Pain Point:</h3>
                                                @foreach($painpoints as $painpoint)
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="exampleCheck1" name="pain_points[]" value="{{$painpoint->id}}" @foreach($openbooking->painpoints as $sp) @if($sp->id==$painpoint->id){{'checked'}} @endif @endforeach>
                                                        <label class="form-check-label" for="exampleCheck1">{{$painpoint->name}}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <!-- One "tab" for each step in the form: -->
                                                <h3>Disease if Any:</h3>
                                                @foreach($diseases as $disease)
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="exampleCheck2" name="ignore_diseases[]" value="{{$disease->id}}"  @foreach($openbooking->diseases as $sd) @if($sd->id==$disease->id){{'checked'}} @endif @endforeach >
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                @foreach($treatments as $treatment)
                                                    @foreach($treatment['treatments'] as $t)

                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">
                                                                <b>{{$loop->iteration}}.</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                              <input type="checkbox" class="form-check-input" id="exampleCheck1" name="treatments[]" value="{{$t['treatment']['id']}}" @foreach($openbooking->treatmentsGiven as $tg) @if($t['treatment']['id']==$tg->id){{'checked'}}@endif @endforeach>
                                                                {{$t['treatment']['description']}}
                                                            </h3>
                                                        </div>
                                                        <!-- /.card-header -->
                                                        <div class="card-body">
                                                            <dl>
                                                                <dt>Main Diseases</dt>
                                                                <dd>
                                                                    {{$treatment['main_disease']}},
                                                                </dd>
                                                                <dt>Other Diseases</dt>
                                                                <dd>
                                                                    {{$t['reason_disease']}},
                                                                </dd>
                                                                <dt>Pain Points</dt>
                                                                <dd>
                                                                    {{$t['painpoint']}}
                                                                </dd>

                                                                <dt>Formulae</dt>
                                                                <dd>
                                                                    {{$t['treatment']['description']}}
                                                                </dd>
                                                                <dt>Exercise</dt>
                                                                <dd>
                                                                    {{$t['treatment']['exercise']}}
                                                                </dd>

                                                                <dt>Dont Exercise</dt>
                                                                <dd>
                                                                    {{$t['treatment']['dont_exercise']}}
                                                                </dd>

                                                                <dt>Diet</dt>
                                                                <dd>
                                                                    {{$t['treatment']['diet']}}
                                                                </dd>

                                                                <dt>Recommended Days</dt>
                                                                <dd>
                                                                    {{$t['treatment']['recommended_days']}}
                                                                </dd>

                                                                <dt>What to do if pain increase?</dt>
                                                                <dd>
                                                                    {{$t['treatment']['action_when_pain_increase']}}
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                        <!-- /.card-body -->
                                                    </div>
                                                    @endforeach
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
                                <h3 class="card-title">Other Diagnose</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>

                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <form action="{{route('therapistwork.other.diagnose', ['id'=>$openbooking->id])}}" method="post">
                                    @csrf
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <h3>Diagnose Points:</h3>
                                                @foreach($diagnose_points as $dp)
                                                    <div class="form-check">
                                                        <label class="form-check-label" for="exampleCheck1">{{$dp->name}}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @php
                                            $diagnosed_values=[];
                                            foreach($openbooking->diagnose as $d){
                                                if(!isset($diagnosed_values[$d->id]))
                                                $diagnosed_values[$d->id]=[];
                                                $diagnosed_values[$d->id]['before_value']=$d->pivot->before_value;
                                                $diagnosed_values[$d->id]['after_value']=$d->pivot->after_value;
                                            }
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <h3>Before Treatment</h3>
                                                @foreach($diagnose_points as $dp)
                                                    <div class="form-check">
                                                        @if($dp->type=='input')
                                                            <input type="text" class="form-check-input" id="exampleCheck1" name="before_treatment[{{$dp->id}}]" value="{{$diagnosed_values[$dp->id]['before_value']??''}}">
                                                        @else
                                                            <select name="before_treatment[{{$dp->id}}]" class="form-select-input">
                                                                <option value="">Select</option>
                                                                <option value="Ok" @if(($diagnosed_values[$dp->id]['before_value']??'')=='Ok'){{'selected'}}@endif>Ok</option>
                                                                <option value="Not Ok" @if(($diagnosed_values[$dp->id]['before_value']??'')=='Not Ok'){{'selected'}}@endif>Not Ok</option>
                                                            </select>
                                                        @endif
                                                        <label class="form-check-label" for="exampleCheck1"></label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <!-- One "tab" for each step in the form: -->
                                                <h3>After Treatment</h3>
                                                @foreach($diagnose_points as $dp)
                                                    <div class="form-check">
                                                        @if($dp->type=='input')
                                                        <input type="text" class="form-check-input" id="exampleCheck1" name="after_treatment[{{$dp->id}}]" value="{{$diagnosed_values[$dp->id]['after_value']}}">
                                                        @else
                                                            <select name="after_treatment[{{$dp->id}}]" class="form-select-input">
                                                                <option value="">Select</option>
                                                                <option value="Ok" @if(($diagnosed_values[$dp->id]['after_value']??'')=='Ok'){{'selected'}}@endif>Ok</option>
                                                                <option value="Not Ok" @if(($diagnosed_values[$dp->id]['after_value']??'')=='Not Ok'){{'selected'}}@endif>Not Ok</option>
                                                            </select>
                                                        @endif
                                                            <label class="form-check-label" for="exampleCheck1"></label>

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
                                <h3 class="card-title">Treatment Result</h3>
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
                                <h3>Select Any:</h3>
                                        <div class="form-check">
                                            <label>
                                                <input type="radio" name="result" value="1" @if($openbooking->therapist_result==1){{'checked'}} @endif />
                                            </label>                                                                         <label class="form-check-label" for="exampleCheck1">No Relief
                                            </label>
                                            <br>
                                            <label>
                                                <input type="radio" name="result" value="2" @if($openbooking->therapist_result==2){{'checked'}} @endif/>
                                            </label>                                                                         <label class="form-check-label" for="exampleCheck1">Relief
                                            </label>
                                            <br>
                                            <label>
                                                <input type="radio" name="result" value="3" @if($openbooking->therapist_result==3){{'checked'}} @endif/>
                                            </label>                                                                         <label class="form-check-label" for="exampleCheck1">Cured
                                            </label>
                                            <br>
                                            <label>
                                                <input type="radio" name="result" value="4" @if($openbooking->therapist_result==4){{'checked'}} @endif/>
                                            </label>                                                                         <label class="form-check-label" for="exampleCheck1">Problem Increased
                                            </label>
                                        </div>
{{--                                @foreach($painpoints as $painpoint)--}}
{{--                                    <div class="form-check">--}}
{{--                                        <label class="form-check-label" for="exampleCheck1">{{$painpoint->name}}</label>--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="1" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==1){{'checked'}} @endif @endforeach/>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                        </label>--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="2" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==2){{'checked'}} @endif @endforeach/>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                        </label>--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="3" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==3){{'checked'}} @endif @endforeach/>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                        </label>--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="4" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==4){{'checked'}} @endif @endforeach/>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                        </label>--}}
{{--                                        <label>--}}
{{--                                            <input type="radio" name="rating[{{$painpoint->id}}]" value="5" @foreach($selected_pain_points as $s) @if($s->pain_point_id==$painpoint->id && $s->related_rating==5){{'checked'}} @endif @endforeach/>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                            <span class="icon">★</span>--}}
{{--                                        </label>--}}

{{--                                    </div>--}}
{{--                                @endforeach--}}
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
