<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;

class CustomReportController extends CMSController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $listRKH = DB::table('EWS_JADWAL_RKM')
        //     ->distinct()
        //     ->select('rkhCode')
        //     ->orderBy('rkhCode', 'asc')
        //     ->get();
        // $data['listRKH'] = $this->removeWhitespace($listRKH);
        // return view('cms.customReport', $data);
        return view('cms.customReport');
    }

    public function postDropdown(Request $request)
    {
        // return $request->all(); # {type: "aktifitas", id: "RKH/KL01/0119/0029"}
        $set = $request->id;

        # Buat pilihan "Switch Case" berdasarkan variabel "type" dari form
        switch($request->type):
            case 'rkh':
                $data = DB::table('EWS_JADWAL_RKM')
                    ->distinct()
                    ->select('rkhCode')
                    ->whereBetween('rkhDate', [$request->dateAwal, $request->dateAkhir])
                    ->orderBy('rkhCode', 'asc')
                    ->get();
                $listData = $this->removeWhitespace($data);
                $return = '<option value="">Pilih RKH...</option>';
                foreach($listData as $temp) 
                    $return .= "<option value=".$temp['rkhCode'].">".$temp['rkhCode']."</option>";
                return $return;
            break;
            case 'aktifitas':
                $data = DB::table('EWS_JADWAL_RKM')
                    ->distinct()
                    ->join('EWS_SUB_JOB', 'EWS_SUB_JOB.subJobCode', '=', 'EWS_JADWAL_RKM.codeAlojob')
                    ->select('EWS_JADWAL_RKM.codeAlojob', 'EWS_SUB_JOB.Description')
                    ->where('rkhCode', '=', $set)
                    ->orderBy('codeAlojob', 'asc')
                    ->get();
                $listData = $this->removeWhitespace($data);
                $return = '<option value="">Pilih Aktifitas...</option>';
                foreach($listData as $temp) 
                    $return .= "<option value=".$temp['codeAlojob'].">".$temp['Description']."</option>";
                return $return;
            break;
            case 'blok':
                $data = DB::table('EWS_JADWAL_RKM')
                    ->distinct()
                    ->select('codeBlok')
                    ->where('codeAlojob', '=', $set)
                    ->orderBy('codeBlok', 'asc')
                    ->get();
                $listData = $this->removeWhitespace($data);
                $return = '<option value="">Pilih Blok...</option>';
                foreach($listData as $temp) 
                    $return .= "<option value=".$temp['codeBlok'].">".$temp['codeBlok']."</option>";
                return $return;
            break;
        endswitch;
    }

    public function postFilter(Request $request)
    {
        # code...
        // return $request->all();
        $table = DB::select('exec EWS_sp_allPokokStatus @rkhCode = ?, @codeAlojob = ?, @codeBlok = ?',array($request->rkhCode,$request->codeAlojob,$request->codeBlok));

        return $table;
    }

    public function chartDataSet(Request $request)
    {
        # code...
        // $table = DB::select('exec EWS_sp_allPokokStatus @rkhCode = ?, @codeAlojob = ?, @codeBlok = ?',array($request->rkhCode,$request->codeAlojob,$request->codeBlok));
        $query = DB::table('EWS_JADWAL_RKM')
            ->select('*')
            ->selectRaw('convert(varchar, rkhDate, 106) as tanggal')
            ->selectRaw('dbo.EWS_f_getTotalPokok(codeBlok, barisStart, barisEnd) as totalPokok')
            ->selectRaw('dbo.EWS_f_totalPokokDone(rkhCode, codeAlojob, codeBlok) as pokokDone')
            ->selectRaw('dbo.EWS_f_realisasiPersen(dbo.EWS_f_getTotalPokok(codeBlok, barisStart, barisEnd), dbo.EWS_f_totalPokokDone(rkhCode, codeAlojob, codeBlok)) as persentase')
            ->where('rkhCode', '=', $request->rkhCode)
            ->where('codeAlojob', '=', $request->codeAlojob)
            ->where('codeBlok', '=', $request->codeBlok)
            ->first();
            $report = $this->removeWhitespace2($query);
        // $table = DB::select('exec EWS_sp_allPokokStatus @rkhCode = ?, @codeAlojob = ?, @codeBlok = ?',array('RKH/KL01/0319/0292', '5210400100', '2031-R0'));

        return $report;
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
