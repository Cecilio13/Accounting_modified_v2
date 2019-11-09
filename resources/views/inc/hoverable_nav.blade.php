<style>
/* The container <div> - needed to position the hooo content */
.hooo {
  position: relative;
  display: inline-block;
}
.hoo_btn{
    font-weight:bold;
}
/* hooo Content (Hidden by Default) */
.hooo-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

/* Links inside the hooo */
.hooo-content a {
  color: black;
  padding: 6px 8px;
  text-decoration: none;
  display: block;
}

/* Change color of hooo links on hover */
.hooo-content a:hover {background-color:#2683b5;color:white;}
.hooo-content{font-weight:bold;}
/* Show the dropdown menu on hover */
.hooo:hover .hooo-content {display: block;}

/* Change the background color of the hooo button when the hooo content is shown */
.hooo:hover .hoo_btn {background-color: #1d648a;}
.hoo_btn:hover{background-color: #1d648a;}
</style>
<div style="width:100%;display:none;background-color:#2683b5;vertical-align:middle;" id="hoverrable_navbar" onmouseover="this.style.display='table-caption';">
    <div>
        <div class="hooo">
          <button class="btn btn-link btn-sm hoo_btn" style="height:max-content;color:white;">Client</button>
          <div class="hooo-content">
                {{-- add new client --}}
                @if (count($UserAccessList)>0)
                @if ($UserAccessList[0]->user_approval=="1")
                <script>
                        function add_client_button(){
                            swal({
                            text: 'Enter Client Name',
                            content: "input",
                            button: {
                                text: "Add Client",
                                closeModal: false,
                            },
                            })
                            .then(name => {
                                if (!name) throw null;
                                
                                $.ajax({
                                    method: "POST",
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: "create_database",
                                    data: {name:name,_token: '{{csrf_token()}}'},
                                    success: function (data) {
                                        //alert(data);
                                        if(data=="Duplicate"){
                                            swal({title: "Error!", text:"Duplicate Client Name", type: 
                                            "error"}).then(function(){
                                            location.reload();                                    
                                            });
                                        }else if(data=="1"){
                                            swal({title: "Done!", text:"Successfully Added Client", type: 
                                            "success"}).then(function(){
                                            location.reload();                                    
                                            });
                                        }else{
                                            swal({title: "Error!", text:"Failed to Add Client", type: 
                                            "error"}).then(function(){
                                            location.reload();                                    
                                            });  
                                        }
                                        
                                        
                                    },
                                    error: function (data) {
                                        alert(data.responseText);
                                    }
                                });
                            })
                            .catch(err => {
                                if (err) {
                                    swal("Error!", "Please Try Again Later", "error");
                                } else {
                                    swal.stopLoading();
                                    swal.close();
                                }
                            });
                        }
                    </script>
                    <a href="#" onclick="add_client_button()">New Client</a>
                    <a href="#" data-toggle="modal" data-target="#change_users_client_access" class="">Change User's Client Access</a>
                @endif
                @endif
            <a href="#" data-toggle="modal" data-target="#changecurrentclientmodal">Change Current Client</a>
          </div>
        </div>
        <button class="btn btn-link btn-sm hoo_btn" style="height:max-content;color:white;display:none;">Option</button>
    </div>
</div>
<div class="modal fade" id="change_users_client_access" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Change User's Client Access</h5>
        </div>
        <script>
            $(document).ready(function(){
                $("#change_user_client_access").submit(function(e) {
                    $.ajax({
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "update_users_client_access",
                        data: $('#change_user_client_access').serialize(),
                        success: function (data) {
                            swal({title: "Done!", text: "Successfully Save Changes.", type: 
                                "success"}).then(function(){ 
                                location.reload();
                            });
                        },
                        error: function (data) {
                            swal("Error!", "Failed to Save Changes", "error");
                        }
                    }); 
                    e.preventDefault();
                    
                });
            })
        </script>
        <form id="change_user_client_access">
            {{ csrf_field() }}
        <div class="modal-body">
            <script>
                function getUsersClientAccess(){
                    var selected=document.getElementById('UserSelectList').value;
                    if(selected!=''){
                        $.ajax({
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: 'get_user_client_access',                
                            data: {user_id:selected,_token: '{{csrf_token()}}'},
                            success: function(data) {
                                var lenth=data.length;
                                console.log(data);
                                console.log(lenth);
                                for(var c=lenth-1;c>=0;c--){
                                    console.log(c+" index"+' client_'+data[c]['client_id']);
                                    var el=document.getElementById('client_'+data[c]['client_id']);
                                    if(el){
                                        el.checked="true";
                                        console.log(data[c]['client_id']+" checked");
                                    }
                                }
                            }
                        }) 
                    }
                }
            </script>
            <div class="row">
                <div class="col-md-4">
                    <select class="w-100 form-control selectpicker" data-live-search="true" onchange="getUsersClientAccess()" id="UserSelectList" name="UserSelectList" required>
                    <option value="">--Select User--</option>
                    @foreach ($all_system_users as $data)
                        <option value="{{$data->id}}">{{$data->name}}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <h5 style="margin-bottom:10px;"></h5>
                    @foreach ($ClientList as $clnt)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{$clnt->clnt_id}}" id="client_{{$clnt->clnt_id}}" name="accessclient[]">
                            <label class="form-check-label" for="client_{{$clnt->clnt_id}}" style="font-size:initial;">
                                {{$clnt->clnt_name}}
                            </label>
                        </div>
                    @endforeach
                    
                </div>
                

                {{-- @foreach ($all_system_users as $data)
                    {{print_r($data)."<br>"}}
                @endforeach
                @foreach ($all_user_client_access as $data)
                    {{print_r($data)."<br>"}}
                @endforeach --}}
                
            </div>
            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Close</button>
            <button class="btn btn-success rounded" type="submit">Save</button>
        </div>
        </form>
        </div>
    </div>
</div>
<div class="modal fade" id="changecurrentclientmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Select Client</h5>
        </div>
        <div class="modal-body">
            <div class="form-inline">
                <script>
                var current_client_name="{{$user_position->clnt_db_id}}";
                function confirm_change_client(e){
                    if(e.value==""){

                    }else{
                        swal({
                            title: "Are you sure?",
                            text: "Are you sure you want to change Client?",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((result) => {
                            if (result) {
                                $.ajax({
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: 'update_user_client_select',                
                                    data: {client_id:e.value,user_id:"{{$user_position->id}}",_token: '{{csrf_token()}}'},
                                    success: function(data) {
                                        swal({title: "Done!", text: "Client will now change.", type: 
                                            "success"}).then(function(){ 
                                            location.href="home";
                                        });
                                    }
                                })
                                
                            } else {
                               e.value=current_client_name;
                               document.getElementById('change_client_select_button').click();
                            }
                        });
                    }
                    
                }
                </script>
                <button style="display:none;" id="change_client_select_button" data-value="change_client_select_id"></button>
                <select class="w-100 form-control selectpicker" id="change_client_select_id" data-live-search="true" onchange="confirm_change_client(this)">
                <option value="">--Select Client--</option>
                @foreach ($ClientList as $clnt)
                    @foreach ($all_user_client_access as $auca)
                        @if ($user_position->id==$auca->user_id && $auca->access_status=="1" && $clnt->clnt_id==$auca->client_id)
                            <option value="{{$clnt->clnt_id}}" {{!empty($user_position)? ($user_position->clnt_db_id==$clnt->clnt_id? 'Selected' : '') :  ''}}>{{$clnt->clnt_name}}</option>
                        @endif
                    @endforeach
                @endforeach
                </select>
                
            </div>
            
        </div>
        
        </div>
    </div>
</div>


