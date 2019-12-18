<?php

namespace App\Http\Controllers;

use App\Clients;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\User;
use App\UserCostCenterAccess;
use App\Company;
use App\Sales;
use App\Expenses;
use App\Advance;
use App\Customers;
use App\ProductsAndServices;
USE App\SalesTransaction;
use App\Supplier;
use App\JournalEntry;
use App\Formstyle;
use App\Report;
use App\AuditLog;
use App\Voucher;
use App\ChartofAccount;
use App\Numbering;
use App\CostCenter;
use App\DepositRecord;
use App\Bank;
use App\UserAccess;
use App\CC_Type;
use App\UserClientAccess;
use App\ExpenseTransaction;
use App\ExpenseTransactionNew;
use App\EtAccountDetail;
class GetController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $db_name="accounting_modified";
            if (Auth::check()) {
                if(Auth::user()->clnt_db_id!=""){
                    $client= Clients::find(Auth::user()->clnt_db_id);
                    $db_name="accounting_modified_".$client->clnt_db_name;
                }
                DB::disconnect('mysql');//here connection name, I used mysql for example
                Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
            } 
            DB::disconnect('mysql');//here connection name, I used mysql for example
            Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
            return $next($request);
        });
    }
    public function get_bill_info_for_supplier_credit(Request $request){
        $supplier_credit_bill_no=$request->supplier_credit_bill_no;
        $bill_info=ExpenseTransaction::where([
            ['et_type','=','Bill'],
            ['et_no','=',$supplier_credit_bill_no]
        ])->first();

        return $bill_info;
    }
    public function get_bill_account_detail(Request $request){
        $supplier_credit_bill_no=$request->supplier_credit_bill_no;
        $bill_info=EtAccountDetail::where([
            ['et_ad_no','=',$supplier_credit_bill_no]
        ])->get();

        return $bill_info;
    }
    public function check_supplier_credit_no(Request $request){
        $invoice_no_field=$request->invoice_no_field;
        $invoice_count=ExpenseTransaction::where([
            ['et_type','=','Supplier credit'],
            ['et_no','=',$invoice_no_field]
        ])->count();
        return $invoice_count;
    }
    public function check_bill_no(Request $request){
        $invoice_no_field=$request->invoice_no_field;
        $invoice_count=ExpenseTransaction::where([
            ['et_type','=','Bill'],
            ['et_no','=',$invoice_no_field]
        ])->count();
        $invoice_count_new=ExpenseTransactionNew::where([
            ['et_type','=','Bill'],
            ['et_no','=',$invoice_no_field]
        ])->count();
        return $invoice_count+$invoice_count_new;
    }
    public function check_sales_receipt_no(Request $request){
        $invoice_no_field=$request->invoice_no_field;
        $invoice_count=SalesTransaction::where([
            ['st_type','=','Sales Receipt'],
            ['st_no','=',$invoice_no_field]
        ])->count();

        return $invoice_count;
    }
    public function check_credit_note_no(Request $request){
        $invoice_no_field=$request->invoice_no_field;
        $invoice_count=SalesTransaction::where([
            ['st_type','=','Credit Note'],
            ['st_no','=',$invoice_no_field]
        ])->count();

        return $invoice_count;
    }
    public function check_estimate_no(Request $request){
        $invoice_no_field=$request->invoice_no_field;
        $invoice_count=SalesTransaction::where([
            ['st_type','=','Estimate'],
            ['st_no','=',$invoice_no_field]
        ])->count();

        return $invoice_count;
    }
    public function check_invoice_no(Request $request){
        $invoice_location_top=$request->invoice_location_top;
        $invoice_type_top=$request->invoice_type_top;
        $invoice_no_field=$request->invoice_no_field;
        $invoice_count=SalesTransaction::where([
            ['st_type','=','Invoice'],
            ['st_location', '=', $invoice_location_top],
            ['st_invoice_type','=',$invoice_type_top],
            ['st_no','=',$invoice_no_field]
        ])->count();

        return $invoice_count;
    }
    public function get_customer_info(Request $request){
        $customers=Customers::find($request->id);
        return $customers;
    }
    public function get_product_info(Request $request){
        return ProductsAndServices::find($request->id);
    }
    public function check_cost_center_name(Request $request){
        $count=0;
        $count+=count(CostCenter::where([['cc_type','=',$request->name]])->get());
        $count+=count(CC_Type::where([['cc_type','=',$request->name]])->get());
        return $count;
    }
    public function check_cost_center_code(Request $request){
        $count=0;
        $count+=count(CostCenter::where([['cc_type_code','=',$request->name]])->get());
        $count+=count(CC_Type::where([['cc_code','=',$request->name]])->get());
        return $count;
    }
    public function save_cc_type(Request $request){
        $data=new CC_Type;
        $data->cc_type=$request->typename;
        $data->cc_code=$request->typecode;
        $data->save();
    }
    public function get_latest_journal_series(Request $request){
            $count=count(JournalEntry::where([
                ['journal_type','=',$request->journal_entry_type]
            ])->groupBy('je_no')->get())+1;

            $journalvoucher_no_series="";
            if($count<10){
                $journalvoucher_no_series="000".$count;
            }
            else if($count>9 && $count<100){
                $journalvoucher_no_series="00".$count;
            }else if($count>99 && $count<1000){
                $journalvoucher_no_series="0".$count;
            }
            $formated_journal_series="";
            if($request->journal_entry_type=="Cheque Voucher"){
                $formated_journal_series="CV".date('y').$journalvoucher_no_series;
            }else{
                $formated_journal_series="JV".date('y').$journalvoucher_no_series;
            }
            return $formated_journal_series;

    }
    public function get_journal_entry_data(Request $request){
        $va=$request->no;
        $result=DB::connection('mysql')->select("SELECT * FROM journal_entries 
        LEFT JOIN chart_of_accounts 
        ON journal_entries.je_account=chart_of_accounts.id 
        LEFT JOIN cost_center 
        ON journal_entries.je_cost_center=cost_center.cc_no 
        WHERE je_no='$va'");
        return $result;
        //SELECT *,FORMAT (`je_attachment`, 'yyyy-MM-dd') FROM `journal_entries` WHERE je_no='$request->no'
    }
    public function getcoa_cc_name(Request $request){
        $data=ChartofAccount::find($request->id);
        $cc=$data->coa_cc;
        $name="";
        if($cc==""){
            $name="";
        }else{
            $cc_list=CostCenter::find($cc);
            $name=$cc_list->cc_name;
        }
        $data = array(
            'name' => $name,
            'no' => $cc,
        );
        return json_encode($data);
    }
    public function export_dat_file(){
        $myfile = fopen("extra/export_report/dat_file.dat", "w") or die("Unable to open file!");
        
        $expense_transactions = DB::table('expense_transactions')
                ->join('et_account_details', 'expense_transactions.et_no', '=', 'et_account_details.et_ad_no')
                ->join('customers', 'customers.customer_id', '=', 'expense_transactions.et_customer')
                ->join('chart_of_accounts','chart_of_accounts.id','=','et_account_details.et_ad_product')
                ->get();
        foreach($expense_transactions as $et){
            $txt = $et->tin_no." , ".($et->display_name!=""? $et->display_name : $et->f_name." ".$et->l_name )." , ".$et->street." ".$et->city." ".$et->state." ".$et->postal_code." ".$et->country." , ".$et->coa_name." , ".number_format($et->et_ad_total,2)."\n";
            fwrite($myfile, $txt);
        }
        
        fclose($myfile);
        return response()->download('extra/export_report/dat_file.dat','report.dat');
    }
    public function get_user_client_access(Request $request){
        $db_name="accounting_modified";
        DB::disconnect('mysql');//here connection name, I used mysql for example
        Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
        $data=UserClientAccess::where([
            ['user_id','=',$request->user_id],
            ['access_status','=','1']
        ])->get();
        return $data;
    }
    public function update_users_client_access(Request $request){
        $db_name="accounting_modified";
        DB::disconnect('mysql');//here connection name, I used mysql for example
        Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
        UserClientAccess::where([
            ['user_id','=',$request->UserSelectList]
        ])->delete();
        if($request->has('accessclient')){
            $accessclient=$request->accessclient;
            foreach($accessclient as $data){
                $clnt=new UserClientAccess;
                $clnt->user_id=$request->UserSelectList;
                $clnt->client_id=$data;
                $clnt->access_status='1';
                $clnt->save();
            }
        }
        
    }
    public function create_database(Request $request){
        $db_name="accounting_modified";
        DB::disconnect('mysql');//here connection name, I used mysql for example
        Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
        $unique_db_id=uniqid($request->name, true);
        $res = preg_replace("/[^a-zA-Z0-9]/", "", $request->name);
        $final_name=$res.substr(uniqid('', true), -5);
        //$this->create_database();
        //create database
        //CREATE DATABASE database_name
        $client=Clients::where([
            ['clnt_name','=',$request->name]
        ])->first();
        if(!empty($client)){
            return "Duplicate";
        }else{
            $data= new Clients;
            $data->clnt_name=$request->name;
            $data->clnt_db_name=$final_name;
            $data->clnt_status="1";
            if($data->save()){
                DB::connection('mysql')
                ->statement(
                    'CREATE DATABASE accounting_modified_'.$final_name
                );
                //show database table lists
                //show tables from accounting;
                $tables = DB::select('SHOW TABLES from accounting_modified');
                $eee="";
                foreach($tables as $table)
                {
                    DB::connection('mysql')
                    ->statement(
                        'CREATE TABLE  accounting_modified_'.$final_name.'.'.$table->Tables_in_accounting_modified.' LIKE accounting_modified.'.$table->Tables_in_accounting_modified
                    );
                    //$eee.=$table->Tables_in_accounting_modified."\n";
                }
                return "1";
            }else{
                return "0";
            }
            
        }
        

        //copy tables
        //CREATE TABLE new_database.new_table LIKE old_database.old_table

    }
    public function confirm_first_admin_account(Request $request){
        $db_name="accounting_modified";
        DB::disconnect('mysql');//here connection name, I used mysql for example
        Config::set('database.connections.mysql.database', $db_name);//new database name, you want to connect to.
        $None="55";
        $email=$request->email;
        $users=User::where([
            ['approved_status','=','1']
        ])->get();
        if(count($users)>0){
            $None="0";//will not activate anymore account
        }else{
            $users=User::where([
                ['email','=',$email]
            ])->first();
            
            if(!empty($users)){
                $id=$users->id;
                $data=User::find($id);
                $data->approved_status="1";
                $data->access_company_setup="1";
                $data->access_bulletin="1";
                $data->access_ceo="1";
                $data->access_hr="1";
                $data->access_payroll="1";
                $data->access_asset_namagement="1";
                if($data->save()){
                    $data=UserAccess::find($id);
                    if(empty($data)){
                        $data= new UserAccess;
                    }
                    $data->user_approval="1";
                    $data->approvals="1";
                    $data->journal_entry="1";
                    $data->sales="1";
                    $data->invoice="1";
                    $data->estimate="1";
                    $data->credit_note="1";
                    $data->sales_receipt="1";
                    $data->expense="1";
                    $data->bill="1";
                    $data->supplier_credit="1";
                    $data->pay_bills="1";
                    $data->reports="1";
                    $data->fund_feeds="1";
                    $data->chart_of_accounts="1";
                    $data->cost_center="1";
                    $data->settings="1";
                    $data->procurement_system="1";
                    $data->procurement_sub="1";
                    $data->approval_pending_bills="1";
                    $data->approval_bank="1";
                    $data->approval_coa="1";
                    $data->approval_cc="1";
                    $data->approval_customer="1";
                    $data->approval_supplier="1";
                    $data->approval_product_services="1";
                    $data->approval_sales="1";
                    $data->approval_expense="1";
                    $data->approval_boq="1";
                    $data->hr_system="1";
                    $data->payroll_system="1";
                    if($data->save()){
                        $data=UserCostCenterAccess::where([
                            ['use_id','=',$id],
                            ['cost_center_id','=','All']
                        ])->first();
                        if(empty($data)){
                            $data= new UserCostCenterAccess;
                        }
                            $data->use_id=$id;
                            $data->cost_center_id="All";
                            $data->access_status="1";
                            $data->save();
                        $None="1";//will activate the setted email
                    }

                }
            }else{
                $None="2";//account not found
            }
            
            
        }
        
        return view('account_confimation_page', compact('None'));
    }
}
