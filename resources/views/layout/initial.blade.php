<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <link rel="apple-touch-icon" href="{{asset('images/icon.png')}}">
    <link rel="shortcut icon" href="{{asset('images/icon.png')}}">
<?php
    //auto refresh
    //echo "<meta http-equiv='refresh' content='1' >";
?>
<!--<meta http-equiv="refresh" content="10" >-->
    <head>
        @include('inc.head')
    </head>
    <body>
        @include('inc.hoverable_nav')
        <aside id="left-panel" class="left-panel" style="min-width:250px">
        @include('inc.side')
        </aside>
        
        <div id="right-panel" class="right-panel" >
            
                <header id="header" class="header" onmouseover="document.getElementById('hoverrable_navbar').style.display='table-caption';" onmouseout="document.getElementById('hoverrable_navbar').style.display='none';">
                        @include('inc.nav')
                        </header>
        @yield('content')
        @include('inc.footer')
        </div>
    </body>
</html>
