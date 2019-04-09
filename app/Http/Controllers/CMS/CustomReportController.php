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
        $date = now();
        $tgl = date_create($date);
        $tgl_ubah = date_format($tgl, 'Y-m-d');

        $data = DB::table('EWS_VW_DETAIL_JADWAL_RKM')
            ->distinct()
            ->select('*')
            ->whereBetween('rkhDate', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
            ->orderBy('Description', 'asc')
            ->get();
        $det_rkm = $this->removeWhitespace($data);
        $data['navigasi'] = $det_rkm;
        $data['navigasi0'] = $det_rkm[0];
        
        $data['now'] = $tgl_ubah;

        return view('cms.customReport', $data);
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

    public function filterByDate(Request $request)
    {
        # code...
        $data = DB::table('EWS_VW_DETAIL_JADWAL_RKM')
            ->distinct()
            ->select('*')
            ->whereBetween('rkhDate', [$request->dateAwal, $request->dateAkhir])
            ->orderBy('rkhCode', 'asc')
            ->get();
        $listData = $this->removeWhitespace($data);

        $return = '';
        foreach($listData as $temp) 
            $return .= "
            <a class='nav-link' id='v-pills-profile-tab' data-toggle='pill' data-rkh='".$temp['rkhCode']."' data-aktifitas='".$temp['codeAlojob']."' data-blok='".$temp['codeBlok']."' href='#' role='tab' aria-controls='v-pills-profile' aria-selected='false'>
                ".$temp['Description']." || ".$temp['rkhCode']." || ".$temp['codeBlok']."
            </a>";
        
        return $return;
    }

}
