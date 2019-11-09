<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Customers;
use App\ProductsAndServices;
use Auth;
use App\Clients;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\UserClientAccess;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    
        // $client=Clients::first();
        // $dbName='accounting_modified_'.$client->clnt_db_name;
            
        // DB::disconnect('mysql');//here connection name, I used mysql for example
        // Config::set('database.connections.mysql.database', $dbName);//new database name, you want to connect to.
    
        $this->middleware(function ($request, $next) {
            $db_name="accounting_modified";
            if(Auth::user()->clnt_db_id!=""){
                $client= Clients::find(Auth::user()->clnt_db_id);
                $db_name="accounting_modified_".$client->clnt_db_name;
            }   
            DB::disconnect('mysql');//here connection name, I used mysql for example
            Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
            return $next($request);
        });
    
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customers::all();
        $products_and_services = ProductsAndServices::all();
        $db_name="accounting_modified";
        DB::disconnect('mysql');//here connection name, I used mysql for example
        Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
        $data= Clients::find(Auth::user()->clnt_db_id);
        $db_name="accounting_modified";
        if(Auth::user()->clnt_db_id!=""){
            $client= Clients::find(Auth::user()->clnt_db_id);
            $db_name="accounting_modified_".$client->clnt_db_name;
        }  
        DB::disconnect('mysql');//here connection name, I used mysql for example
        Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
        $status="0";
        $db_name="";
        //return redirect()->intended('/dashboard');
        //return Auth::user()->clnt_db_id;
        if(!empty($data)){
            if($data->clnt_db_name!=""){
                    $db_name="accounting_modified_".$data->clnt_db_name;
                $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
                $db = DB::select($query, [$db_name]);
                if (empty($db)) {
                    $status="0";
                } else {
                    $status="1";
                    
                }
                
            }else{
                $status="0";
            }
        }else{
            $status="0";
        }

        if($status=="0"){
            //redirect to client selection
            return redirect()->intended('choose_client');
            return "Client Selection Page Placeholder";
        }
        else if($status=="1"){
            //execute change in database name and redirect to dashboard
            //return $db_name." selected";
            return redirect()->intended('/dashboard');
        }
        
    }
}
