@extends('layouts.admin')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Therapists</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Therapists</li>
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
                                        <a href="{{route('therapists.create')}}" class="btn btn-primary">Add Therapists</a> </div>
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
                                        <th>Bookings</th>
                                        <th>Email</th>
{{--                                        <th>mobile</th>--}}
                                        <th>Address</th>
{{--                                        <th>city</th>--}}
{{--                                        <th>state</th>--}}
                                        <th>Image</th>
                                        <th>Isactive</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($therapists as $therapist)
                                        <tr>
                                            <td>{{$therapist->name}}<br><span style="color:darkblue">Reviews:{{$therapist->reviews()->count()}}</span><br><span style="color:green">Reviews:{{number_format($therapist->reviews()->avg('rating'),1)}}</span></td>
                                            <td>{{$therapist->bookings()->where('status', 'completed')->count()}}</td>
                                            <td>{{$therapist->email}}<br>{{$therapist->mobile}}</td>
{{--                                            <td>{{$therapist->mobile}}</td>--}}
                                            <td>{{$therapist->address}}<br>{{$therapist->city}},{{$therapist->state}}</td>
{{--                                            <td>{{$therapist->city}}</td>--}}
{{--                                            <td>{{$therapist->state}}</td>--}}
                                            <td><img src="{{$therapist->image}}" height="80px" width="80px"/></td>
                                            <td>
                                                @if($therapist->isactive==1)<span style="color:green">Yes</span>
                                                @else<span style="color:red">No</span>
                                                @endif
                                            </td>
                                            <td><a href="{{route('therapists.edit',['id'=>$therapist->id])}}" class="btn btn-primary">Edit</a>&nbsp;&nbsp;<a href="{{route('sessions.list',['type'=>'therapist-session', 'id'=>$therapist->id])}}" class="btn btn-primary">Sessions</a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>
                       {{-- {{$therapistadmin->links()}}--}}
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

