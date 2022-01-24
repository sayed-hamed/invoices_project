<?php

namespace App\Http\Controllers;

use App\invoices;
use App\Notifications\Add_invoices_new;
use App\products;
use App\sections;
use App\invoice_attachments;
use App\invoices_details;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Addinvoice;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices=invoices::all();
        return view('invoices.invoices',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        $name=Auth::user()->name;
//        $email=Auth::user()->email;
        $sections=sections::all();
        $products=products::all();
        return view('invoices.add_invoices',compact('sections','products'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        invoices::create([
            'invoice_number'=>$request->invoice_number,
            'invoice_Date'=>$request->invoice_Date,
            'Due_date'=>$request->Due_date,
            'product'=>$request->product,
            'section_id'=>$request->Section,
            'Amount_collection'=>$request->Amount_collection,
            'Amount_Commission'=>$request->Amount_Commission,
            'Discount'=>$request->Discount,
            'Value_VAT'=>$request->Value_VAT,
            'Rate_VAT'=>$request->Rate_VAT,
            'Total'=>$request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note'=>$request->note,
            'Payment_Date'=>$request->Payment_Date,

        ]);

        $invoice_id=invoices::latest()->first()->id;
        invoices_details::create([

        'id_Invoice'=>$invoice_id,
        'invoice_number'=>$request->invoice_number,
         'product'=>$request->product,
         'Section'=>$request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
         'Payment_Date'=>$request->Payment_Date,
         'note'=>$request->note,
         'user'=> (Auth::user()->name),
       ]);
        if ($request->hasFile('pic'));
        $invoice_id=invoices::latest()->first()->id;
        $image=$request->file('pic');
        $filename=$image->getClientOriginalName();
        $invoice_number=$request->invoice_number;

        $attachments=new invoice_attachments();
        $attachments->file_name=$filename;
        $attachments->invoice_number=$invoice_number;
        $attachments->Created_by=(Auth::user()->name);
        $attachments->invoice_id=$invoice_id;

        $attachments->save();

        //set picture to public folder
        $imageName=$request->pic->getClientOriginalName();
        $request->pic->move(public_path('Attachments/'.$invoice_number),$imageName);

//        $users=User::first();
//        Notification::send($users, new Addinvoice($invoice_id));

        $user =User::get();
        $invoices=invoices::latest()->first();
    //        $user->notify(new \App\Notifications\Add_invoices_new($invoices));
        Notification::send($user, new Add_invoices_new($invoices));

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices=invoices::where('id',$id)->first();
        return view('invoices.status_invoices',compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices=invoices::where('id',$id)->first();
        $sections=sections::all();
        return view('invoices.edit_invoices',compact('invoices','sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $invoices = invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id=$request->invoice_id;
//        $id=invoices::findorfail($id);
//        dd($id);
        $invoices=invoices::where('id',$id)->first();
//        dd($invoice);
        $details=invoice_attachments::where('invoice_id',$id)->first();

        $id_page=$request->id_page;


        if (!empty($details->invoice_number)){
            Storage::disk('public_uploads')->deleteDirectory($details->invoice_number.'/'.$details->file_name);
        }
        session()->flash('delete_invoice','تم الحذف بنجاح');
        return redirect('/invoices');



    }


    public function getproducts($id)
    {
        $products = DB::table('products')->where('section_id', $id)->pluck('product_name', 'id');
        return json_encode($products);
    }


    public function Status_Update($id, Request $request)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }

        else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');

    }

    public function Invoice_Paid(){
        $invoices=invoices::where('Value_Status',1)->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }

    public function Invoice_UnPaid(){
        $invoices=invoices::where('Value_Status',3)->get();
        return view('invoices.invoices_unpaid',compact('invoices'));
    }

    public function Invoice_Partial(){
        $invoices=invoices::where('Value_Status',2)->get();
        return view('invoices.invoices_partialpaid');
    }

    public function Print_invoice($id){
        $invoices=invoices::where('id',$id)->first();
        return view('invoices.Print_invoice',compact('invoices'));
    }


    public function export()
    {
        return \Excel::download(new InvoicesExport, 'قائمة الفواتير.xlsx');
    }


    public function MarkAsRead_all (Request $request)
    {

        $userUnreadNotification= auth()->user()->unreadNotifications;

        if($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }


    }



}
