<?php

namespace App\Http\Controllers;
use App\invoices;

use Illuminate\Http\Request;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return\Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $count_all=invoices::all()->count();
        $count_p=invoices::where('Status','مدفوعة')->count();
        $count_pa=$count_p / $count_all *100;

        $count_up=invoices::where('Status','غير مدفوعة')->count();
        $count_upa=$count_up / $count_all *100;

        $count_sp=invoices::where('Status','مدفوعة جزئيا')->count();
        $count_spa=$count_up / $count_all *100;



        $chartjs = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 350, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    "label" => "الفواتير الغير المدفوعة",
                    'backgroundColor' => ['#ec5858'],
                    'data' => [$count_upa]
                ],
                [
                    "label" => "الفواتير المدفوعة",
                    'backgroundColor' => ['#81b214'],
                    'data' => [$count_pa]
                ],
                [
                    "label" => "الفواتير المدفوعة جزئيا",
                    'backgroundColor' => ['#ff9642'],
                    'data' => [$count_spa]
                ],


            ])
            ->options([]);


        $chartjs_2 = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 340, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    'backgroundColor' => ['#ec5858', '#81b214','#ff9642'],
                    'data' => [$count_upa, $count_pa,$count_spa]
                ]
            ])
            ->options([]);

        return view('home', compact('chartjs','chartjs_2'));




    }


}
