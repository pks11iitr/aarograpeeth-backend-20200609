<script>
    function getBooking(id, type)
    {
        $("#booking-form-section").html('')
        $.ajax({

            url: '{{route('session.booking.edit')}}',
            method: 'get',
            data:{type:type, id:id},
            success: function(data){
                $("#booking-form-section").html(data)
                $("#modal-lg").modal('show')
            },

        });
    }

    function getTimeSlotList(){
        $("#therapist-list").html('')
        $("#time-slots").html('')
        $.ajax({

            url: '{{route('clinicadmin.available.slots')}}',
            method: 'get',
            datatype:'json',
            data:{clinic_id:$("#slot-clinic-id").val(), date:$("#slot-date").val(), grade:$("#booking-grade").val()},
            success: function(data){
                html='<option value="">Select Time</option>'
                for(var i = 0; i < data.length; i++) {
                    if(data[i].is_active==1){
                        html=html+'<option value="'+data[i].id+'">'+data[i].date+' '+data[i].start_time+'</option>'
                    }else{
                        html=html+'<option value="'+data[i].id+'" disabled>'+data[i].date+' '+data[i].start_time+'</option>'
                    }

                }

                $("#time-slots").html(html)
            },

        });

    }

    function getAvailableTherapist(){
        $("#therapist-list").html('')
        $.ajax({

            url: '{{route('clinicadmin.available.therapist')}}',
            method: 'get',
            datatype:'json',
            data:{clinic_id:$("#slot-clinic-id").val(), slot_id:$("#time-slots").val(), },
            success: function(data){
                html='<option value="">Select Therapist</option>'
                for(var i = 0; i < data.length; i++) {

                    html=html+'<option value="'+data[i].id+'">'+data[i].name+'</option>'

                }

                $("#therapist-list").html(html)
            },

        });
    }

</script>
