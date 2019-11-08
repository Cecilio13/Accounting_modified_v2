<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <link rel="apple-touch-icon" href="{{asset('images/icon.png')}}">
    <link rel="shortcut icon" href="{{asset('images/icon.png')}}">
    <script src="{{asset('js/jquery.js')}}"></script>
<?php
    //auto refresh
    //echo "<meta http-equiv='refresh' content='1' >";
?>
<!--<meta http-equiv="refresh" content="10" >-->
    <head>
        @include('inc.head')
        <style>
        body {
        font-family: Arial, Helvetica, sans-serif;
        }

        .flip-card {
        background-color: transparent;
        width: 200px;
        height: 200px;
        perspective: 1000px;
        margin:10px;
        cursor: pointer;
        display: inline-block;
        }

        .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.6s;
        transform-style: preserve-3d;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }

        .flip-card:hover .flip-card-inner {
        transform: rotateY(180deg);
        }

        .flip-card-front, .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        }

        .flip-card-front {
        background-color: #bbb;
        color: black;
        }

        .flip-card-back {
        background-color: #2683b5;
        color: white;
        transform: rotateY(180deg);
        margin:0px auto;
        padding-top:40%;
        }
        </style>
    </head>
    <body >
        @include('inc.hoverable_nav_client_page')
        <div style="text-align:center;vertical-align:middle;display:table-cell;height:80vh">
            <div class="flip-card" onclick="add_client_button()">
            <div class="flip-card-inner">
                <div class="flip-card-front">
                <img src="{{asset('images/square_plus.png')}}" alt="Avatar" style="width:200px;height;200px;">
                </div>
                <div class="flip-card-back">
                <div>
                
                <h5 style="font-weight:bold;">Add New Client</h5> 
                </div>
                
                </div>
            </div>
            </div>

            <div class="flip-card" data-toggle="modal" data-target="#changecurrentclientmodal">
            <div class="flip-card-inner">   
                <div class="flip-card-front">
                <img src="{{asset('images/select_client.png')}}" alt="Avatar" style="width:200px;height;200px;">
                </div>
                <div class="flip-card-back">
                <div>
                
                <h5 style="font-weight:bold;">Select Client</h5> 
                </div>
                
                </div>
            </div>
            </div>
            <div class="flip-card">
            <div class="flip-card-inner">   
                <div class="flip-card-front">
                <img src="{{asset('images/logout_client.png')}}" alt="Avatar" style="width:200px;height;200px;">
                </div>
                <div class="flip-card-back">
                <div>
                
                <h5 style="font-weight:bold;">Logout</h5> 
                </div>
                
                </div>
            </div>
            </div>
        </div>
        
        <body>

        

        
        
        
        
        @include('inc.footer')
    </body>
</html>