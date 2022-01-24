<?php

namespace App\Http\Controllers;
use App\sections;
use App\invoices;
use Illuminate\Http\Request;

class CustomerReport extends Controller
{
    public function index(){
        $sections=sections::all();
        $invoices=invoices::all();
        return view('Reports.customer_report',compact('sections','invoices'));
    }

    public function Search_customers(Request $request){
        if ($request->Section &&  $request->start_at='' && $request->end_at)
        {
            $sections=sections::all();
            $invoices=invoices::select('*')->where('section_id','=',$request->Section)->get();
            return view('Reports.customer_report',compact('sections'))->withDetails($invoices);

        }
        else{

            $start_at = date($request->start_at);
            $end_at = date($request->end_at);

            $invoices = invoices::whereBetween('invoice_Date',[$start_at,$end_at])->where('section_id','=',$request->Section)->get();
            $sections = sections::all();
            return view('Reports.customer_report',compact('sections'))->withDetails($invoices);


        }
    }
}
