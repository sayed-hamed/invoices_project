<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\invoices;

class invoices_report extends Controller
{
    public function index(){
        return view('Reports.invoices_report');
    }


    public function Search_invoices(Request $request)
    {
        if ($request->rdio==1){
            if ($request->type && $request->start_at=='' && $request->end_at==''){
                $invoices=invoices::select('*')->where('Status','=',$request->type)->get();
                $type=$request->type;
                return view('Reports.invoices_report',compact('type'))->withDetails($invoices);
            }
            else{
                $start_date=date($request->start_at);
                $end_date=date($request->end_at);
                $type=$request->type;
                $invoices=invoices::whereBetween('invoice_Date',[$start_date,$end_date])->where('Status','=',$request->type)->get();
                return view('Reports.invoices_report',compact('type','start_date','end_date'))->withDetails($invoices);
            }

        }
        else {

            $invoices = invoices::select('*')->where('invoice_number','=',$request->invoice_number)->get();
            return view('reports.invoices_report')->withDetails($invoices);

        }

    }
}
