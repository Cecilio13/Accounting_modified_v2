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
    <div class="btn-group">
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
                @endif
                @endif
            <a href="#" data-toggle="modal" data-target="#changecurrentclientmodal">Change Current Client</a>
          </div>
        </div>
        <button class="btn btn-link btn-sm hoo_btn" style="height:max-content;color:white;display:none;">Option</button>
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
                    <option value="{{$clnt->clnt_id}}" {{!empty($user_position)? ($user_position->clnt_db_id==$clnt->clnt_id? 'Selected' : '') :  ''}}>{{$clnt->clnt_name}}</option>
                @endforeach
                </select>
                
            </div>
            
        </div>
        
        </div>
    </div>
</div>


