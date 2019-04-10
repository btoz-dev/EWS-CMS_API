<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;

class TransReportController extends CMSController
{
    /**
     * Display a listing of the mandor resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mandorPlantcare(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('EWS_VW_CMS_MANDOR_TRANS')
                ->select('id', 'rkhCode', 'mandor', 'tk', 'Description', 'codeBlok', 'codeTanaman', 'mandorNote')
                ->selectRaw('cast(cast(created_at as time) as char(5)) created_at');
            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }

        return view('cms.mandorPlantcareReport');
    }

    /**
     * Display a listing of the kawil resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function kawilPlantcare(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('EWS_VW_CMS_KAWIL_TRANS')
                ->select('id','kawil','kawilNote','rkhCode','Description','mandor','tk','codeBlok','codeTanaman','mandorNote')
                ->selectRaw('cast(cast(created_at as time) as char(5)) created_at');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }
        
        return view('cms.kawilPlantcareReport');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
