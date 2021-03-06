<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use App\Exports\RKMExport;
use App\Http\Controllers\CMSController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RKMReportController extends CMSController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $query = Trans::rkm();
            $query = DB::table('EWS_JADWAL_RKM')
            ->select('EWS_JADWAL_RKM.id', 'EWS_JADWAL_RKM.rkhCode', 'EWS_VW_DETAIL_MANDOR.namaPekerja', 'EWS_SUB_JOB.Description', 'EWS_JADWAL_RKM.codeBlok', 'EWS_JADWAL_RKM.barisStart', 'EWS_JADWAL_RKM.barisEnd', 'EWS_JADWAL_RKM.rkhDate')
            ->selectRaw('convert(varchar, EWS_JADWAL_RKM.rkhDate, 106) as tanggal')
            ->selectRaw('dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd) as totalPokok')
            ->selectRaw('dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok) as pokokDone')
            ->selectRaw('dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd) - dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok) as pokokNDone')
            ->selectRaw('dbo.EWS_f_realisasiPersen(dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd), dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok)) as persentase')
            ->join('EWS_VW_DETAIL_MANDOR', 'EWS_VW_DETAIL_MANDOR.codeMandor', '=', 'EWS_JADWAL_RKM.mandorCode')
            ->join('EWS_SUB_JOB', 'EWS_SUB_JOB.subJobCode', '=', 'EWS_JADWAL_RKM.codeAlojob');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('EWS_JADWAL_RKM.rkhDate', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }else {
                $query->whereBetween('EWS_JADWAL_RKM.rkhDate', [date('Y-m-d'), date('Y-m-d')." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }
        
        return view('cms.rkmReport');
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

    public function exportRKM(Request $request)
    {
        # code...
        $export = new RKMExport();
        $export->setHeading($request->heading);
        $export->setDateAw($request->date_aw);
        $export->setDateAk($request->date_ak);

        return Excel::download($export, 'trans.xlsx');
    }
}
