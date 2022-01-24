<?php

namespace App\Http\Controllers;

use App\products;
use App\sections;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections=sections::all();
        $products=products::all();

        return view('products.products',compact('sections','products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData=$request->validate([
            'Product_name'=>'required',
            'description'=>'required',
            'section_id'=>'required',
            ],[
                'product_name.required'=>'يرجي ادخال اسم المنتج',
                'description.required'=>'يرجي ادخال الوصف',
                'section_id.required'=>'يرجي ملئ الحقل  ',

        ]);

        products::create([
            'Product_name'=>$request->input('Product_name'),
            'description'=>$request->input('description'),
            'section_id'=>$request->input('section_id'),
        ]);
        session()->flash('add','تم ادخال البيانات بنجاح');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\products  $products
     * @return \Illuminate\Http\Response
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id=sections::where('section_name',$request->section_name)->first()->id;
        $products=products::findOrFail($request->pro_id);
        $products->update([
            'Product_name'=>$request->Product_name,
            'description'=>$request->description,
            'section_id'=>$id,
        ]);

        session()->flash('edit','تم تعديل البيانات بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $products=products::findOrFail($request->pro_id);
        $products->delete();
        session()->flash('delete','تم الحذف بنجاح');
        return back();
    }
}
