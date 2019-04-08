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
    public function indexMandor(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('EWS_VW_CMS_MANDOR_TRANS')
            ->get();
            // $query = DB::table('EWS_TRANS_MANDOR')
            // ->join('users', 'users.id', '=', 'EWS_TRANS_MANDOR.userid')
            // ->join('EWS_MANDOR', 'EWS_MANDOR.codePekerja', '=', 'users.codePekerja')
            // ->join('EWS_PEKERJA as m', 'm.codePekerja', '=', 'users.codePekerja')
            // ->join('EWS_PEKERJA as t', 't.codePekerja', '=', 'EWS_TRANS_MANDOR.codeTukang')
            // ->join('EWS_SUB_JOB', 'EWS_SUB_JOB.subJobCode', '=', 'EWS_TRANS_MANDOR.subJobCode')
            // ->select('EWS_TRANS_MANDOR.*', 'users.codePekerja as codePekerjaMandor', 'EWS_MANDOR.codeMandor', 'm.namaPekerja as namaMandor', 't.namaPekerja as namaTukang', 'EWS_SUB_JOB.Description')
            // ->selectRaw('convert(varchar, EWS_TRANS_MANDOR.created_at, 106) as tanggal')
            // ->selectRaw('convert(varchar, EWS_TRANS_MANDOR.created_at, 8) as waktu')
            // ->get();
            $report = $this->removeWhitespace($query);
            return DataTables::of($report)
            ->make(true);
        }
        return view('cms.mandorTransReport');
    }

    /**
     * Display a listing of the kawil resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexKawil(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('EWS_VW_CMS_KAWIL_TRANS')
            ->get();
            $report = $this->removeWhitespace($query);
            return DataTables::of($report)
            ->make(true);
        }
        return view('cms.kawilTransReport');
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
