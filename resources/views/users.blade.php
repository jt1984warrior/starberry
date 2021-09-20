@extends('layouts.master')

@section('title', 'Users')

@section('sidebar')
    @parent

  
@stop

@section('content')
<div class="float-right m-5">
    <button type="button" class="btn btn-primary" id="clearPop" data-toggle="modal" data-target="#userModal">
                      Add User
                    </button>
</div>
<div class="flash-message"></div>

<div class="">
    <table id="userData" class="display" style="width:100%">
        <thead>
            <tr>
            <th>ID</th>
                <th>Name</th>
                <th>Height</th>
                <!-- <th>Films</th> -->
                <th>Hair color</th>
                <th>Skin color </th>
                <th>Action</th>
            </tr>
        </thead>
        
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add People</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" style="display:none"></div>
                    <form class="image-upload" method="post" action="{{ route('storeUser') }}"  id="userform" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id" class="form-control"/>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control"/>
                        </div>  
                        <div class="form-group">
                            <label>Height</label>
                            <input type="text" name="height" id="height" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Films</label>
                            <input type="text" name="films" id="films" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Hair color</label>
                            <input type="text" name="hair_color" id="hair_color" class="form-control"/>
                        </div>

                        <div class="form-group">
                            <label>Skin color</label>
                            <input type="text" name="skin_color" id="skin_color" class="form-control"/>
                        </div>

                        
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <!-- <input type="submit" class="btn btn-success" id="formSubmitB">Save</button> -->

                     <input type="submit" value ="Save" id="formSubmit" class="btn btn-success">
                </div>
            </div>
        </div>
    </div>


@stop

@section('after-scripts')
<script>
 
$(document).ready(function(){
   
    $('#userData').DataTable( {
        "processing": true,
        "serverSide": true,
        "order": [[ 0, "desc" ]],
        "ajax": "{{ route('userlist')}}",
        "columns":[
            {"data":"id"},
            {"data":"name"},
            {"data":"height"},
            // {"data":"films"},
            {"data":"hair_color"},
            {"data":"skin_color"},
            {"data":"action"},
        ],
    } );



 var rules = {
    name: {
             required: true
         },
         height: {
             required: true
         },
         films: {
             required: true
         },
         hair_color: {
             required: true
         },
         skin_color: {
             required: true
         }

     };
     var messages = {
        name: {
             required: "Please enter name"
         },
         height: {
             required: "Please enter height"
         },
         films: {
             required: "Please enter films"
         },
         hair_color: {
             required: "Please enter hair color"
         },
         skin_color: {
             required: "Please enter skin color"
         }
     };
     $("#userform").validate({
         rules: rules,
         messages: messages
     });


    $('#formSubmit').click(function(e){

        $("#userform").valid();
                e.preventDefault();

                var formData =new FormData($('form')[0]);;
                var postData = new FormData($('form')[0]);


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('storeUser') }}",
                    method: 'post',
                    processData: false,
                    data:postData,
                    contentType: false,
                    enctype: 'multipart/form-data',
              
                    success: function(result){
                        if(result.errors)
                        {
                            $('.alert-danger').html('');

                            $.each(result.errors, function(key, value){
                                $('.alert-danger').show();
                                $('.alert-danger').append('<li>'+value+'</li>');
                            });
                        }
                        else
                        {
                            $('#userModal').modal('hide');
                            $('div.flash-message').html(result);
                        $('div.flash-message').show().delay(5000).fadeOut();

                        $( '#userform' ).each(function(){
    this.reset();
});

                        $('#userData').DataTable().ajax.reload();
                        }
                  
                    }
                });
            });

});

function deleteRow(id){
    jconfirm("Are you sure to delete?", function (r) {
            
            if (r) {
                $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax(
    {
        url: "user/delete/"+id,
        type: 'delete', // replaced from put
        dataType: "JSON",
        data: {
            "id": id ,// method and token not needed in data
            "_token": "{{ csrf_token() }}",
        },
        success: function (response)
        {
            $('#userData').DataTable().ajax.reload();
       
            $('div.flash-message').html(response);
                        $('div.flash-message').show().delay(5000).fadeOut();
        },
        error: function(xhr) {

             $('#userData').DataTable().ajax.reload();
           
            $('div.flash-message').html(xhr.responseText);
                        $('div.flash-message').show().delay(5000).fadeOut();
         console.log(xhr.responseText); // this line will save you tons of hours while debugging
        // do something here because of error
       }
    });
            }
        });
}

function editRow(rowId)
{
   

    $.ajax(
    {
        url: "/user/edit/"+rowId,
        type: 'get', // replaced from put
        dataType: "JSON",
        data: {
            "id": rowId ,// method and token not needed in data
            "_token": "{{ csrf_token() }}",
        },
        success: function (response)
        {
            $("label.error").hide();
  $(".error").removeClass("error");
        
        $("#userform input:not([type=button]):not([type=submit]):not([name=_token])").each(function(){
            var id = $(this).attr('id');
                $(this).val(response['result'][0][id]);
            

        });
        //$("input[name=_token]").val(response.token);
        $('#userModal').modal('show');
        $('.modal-title').html('Edit People');
        },
        error: function(xhr) {

         
       }
    });
}
var jconfirm = function (message, callback) {
    var options = {            
        message: message
    };
    options.buttons = {
        cancel: {
            label: "No",
            className: "btn-default",
            callback: function(result) {
                callback(false);
            }
        },
        main: {
            label: "Yes",
            className: "btn-primary",
            callback: function (result) {
                callback(true);
            }
        }
    };
    bootbox.dialog(options);
};

$("#clearPop").click(function(){
    $("#userform input:not([type=button]):not([type=submit]):not([name=_token])").val('');
    $('.modal-title').html('Add People');
});

</script>
@endsection