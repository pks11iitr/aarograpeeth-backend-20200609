@extends('layouts.therapistadmin')
@section('content')

    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>--}}
    <style>
        /** {
            box-sizing: border-box;
        }*/

        body {
            background-color: #f1f1f1;
        }

        #regForm {
            background-color: #ffffff;
            margin: 100px auto;
            font-family: Raleway;
            padding: 40px;
            width: 70%;
            min-width: 300px;
        }

        h3 {
            text-align: left;
        }

        input {
            padding: 10px;
            width: 100%;
            font-size: 17px;
            font-family: Raleway;
            border: 1px solid #aaaaaa;
        }

        /* Mark input boxes that gets an error on validation: */
        /*input.invalid {
            background-color: #ffdddd;
        }*/

        /* Hide all steps by default: */
        .tab {
            display: none;
        }

        button {
            background-color: #4CAF50;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 17px;
            font-family: Raleway;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.8;
        }

        #prevBtn {
            background-color: #bbbbbb;
        }

        /* Make circles that indicate the steps of the form: */
        .step {
            height: 15px;
            width: 15px;
            margin: 0 2px;
            background-color: #bbbbbb;
            border: none;
            border-radius: 50%;
            display: inline-block;
            opacity: 0.5;
        }

        .step.active {
            opacity: 1;
        }

        /* Mark the steps that are finished and valid: */
        .step.finish {
            background-color: #4CAF50;
        }
    </style>
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
                                <h3 class="card-title">Therapistwork Details</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Therapistwork Details</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Order ID</td>
                                        <td>{{$openbooking->therapieswork->therapiesorder->refid??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Home Booking Date</td>
                                        <td>{{$openbooking->therapieswork->date??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Home Booking time</td>
                                        <td>{{$openbooking->therapieswork->time??''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Therapy Name</td>
                                        <td>{{$openbooking->therapieswork->therapiesorder->details[0]->entity->name??''}}</td>
                                    </tr>

                                    <tr>
                                        <td>Start Time</td>
                                        <td>{{$openbooking->start_time}}</td>
                                    </tr>
                                    <tr>
                                        <td>End Time</td>
                                        <td>{{$openbooking->end_time}}</td>
                                    </tr>
                                    <tr>
                                        <td>Message</td>
                                        <td>{{$openbooking->message}}</td>
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
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Disease Point Add</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form role="form" method="post" id="regForm" enctype="multipart/form-data" action="{{route('therapistwork.detailstore',['id'=>$openbooking->id])}}">
                                    @csrf
                                    {{------------------------------------------------------------------------------------}}
                                    <!-- One "tab" for each step in the form: -->
                                    <div class="tab" class="form-group">
                                        <h3>Pain Point:</h3>
                                        @foreach($painpoints as $painpoint)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="exampleCheck1" name="pain_point_id" value="{{$painpoint->id}}">
                                                <label class="form-check-label" for="exampleCheck1">{{$painpoint->name}}</label>
                                            </div>
                                        @endforeach
                                        <br>

                                        <!-- One "tab" for each step in the form: -->
                                    <h3>Disease it Any:</h3>
                                        @foreach($diseases as $disease)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="exampleCheck2" name="disease_id" value="{{$disease->id}}">
                                                <label class="form-check-label" for="exampleCheck2">{{$disease->name}}</label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="tab">Contact Info:
                                        <p><input placeholder="E-mail..." oninput="this.className = ''" name="email"></p>
                                        <p><input placeholder="Phone..." oninput="this.className = ''" name="phone"></p>
                                    </div>
                                    <div class="tab">Birthday:
                                        <p><input placeholder="dd" oninput="this.className = ''" name="dd"></p>
                                        <p><input placeholder="mm" oninput="this.className = ''" name="nn"></p>
                                        <p><input placeholder="yyyy" oninput="this.className = ''" name="yyyy"></p>
                                    </div>
                                    <div class="tab">Login Info:
                                        <p><input placeholder="Username..." oninput="this.className = ''" name="uname"></p>
                                        <p><input placeholder="Password..." oninput="this.className = ''" name="pword" type="password"></p>
                                    </div>
                                    <div style="overflow:auto;">
                                        <div style="float:right;">
                                            <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                                            <button type="submit" id="nextBtn" onclick="nextPrev(1)">Submit</button>
                                        </div>
                                    </div>
                                    <!-- Circles which indicates the steps of the form: -->
                                    <div style="text-align:center;margin-top:40px;">
                                        <span class="step1"></span>
                                        <span class="step2"></span>
                                        <span class="step3"></span>
                                        <span class="step4"></span>
                                    </div>
                                    {{-----------------------------------------------------------------------------------}}
                                    <!-- /.card-body -->
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

        <script>
            var currentTab = 0; // Current tab is set to be the first tab (0)
            showTab(currentTab); // Display the current tab

            function showTab(n) {
                // This function will display the specified tab of the form...
                var x = document.getElementsByClassName("tab");
                x[n].style.display = "block";
                //... and fix the Previous/Next buttons:
                if (n == 0) {
                    document.getElementById("prevBtn").style.display = "none";
                } else {
                    document.getElementById("prevBtn").style.display = "inline";
                }
                if (n == (x.length - 1)) {
                    document.getElementById("nextBtn").innerHTML = "Submit";
                } else {
                    document.getElementById("nextBtn").innerHTML = "Submit";
                }
                //... and run a function that will display the correct step indicator:
                fixStepIndicator(n)
            }

            function nextPrev(n) {
                // This function will figure out which tab to display
                var x = document.getElementsByClassName("tab");
                // Exit the function if any field in the current tab is invalid:
                if (n == 1 && !validateForm()) return false;
                // Hide the current tab:
                x[currentTab].style.display = "none";
                // Increase or decrease the current tab by 1:
                currentTab = currentTab + n;
                // if you have reached the end of the form...
                if (currentTab >= x.length) {
                    // ... the form gets submitted:
                    document.getElementById("regForm").submit();
                    return false;
                }
                // Otherwise, display the correct tab:
                showTab(currentTab);
            }

            function validateForm() {
                // This function deals with validation of the form fields
                var x, y, i, valid = true;
                x = document.getElementsByClassName("tab");
                y = x[currentTab].getElementsByTagName("input");
                // A loop that checks every input field in the current tab:
                for (i = 0; i < y.length; i++) {
                    // If a field is empty...
                    if (y[i].value == "") {
                        // add an "invalid" class to the field:
                        y[i].className += " invalid";
                        // and set the current valid status to false
                        valid = false;
                    }
                }
                // If the valid status is true, mark the step as finished and valid:
                if (valid) {
                    document.getElementsByClassName("step")[currentTab].className += " finish";
                }
                return valid; // return the valid status
            }

            function fixStepIndicator(n) {
                // This function removes the "active" class of all steps...
                var i, x = document.getElementsByClassName("step");
                for (i = 0; i < x.length; i++) {
                    x[i].className = x[i].className.replace(" active", "");
                }
                //... and adds the "active" class on the current step:
                x[n].className += " active";
            }
        </script>
        <!-- ./wrapper -->
        <script>
            $(document).ready(function(){
                $('input:checkbox').click(function() {
                    $('input:checkbox').not(this).prop('checked', false);
                });
            });
        </script>
        <!-- ./wrapper -->
    </div>
    <!-- ./wrapper -->

@endsection
