<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Illuminate\Http\Request;

class CMSController extends Controller
{
    const ID_ROLE_ADMIN = 1;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     // return var_dump($request->user()->checkRoles('1'));
    //     if (!$request->user()->checkRoles(ID_ROLE_ADMIN)) {
    //         # code...
    //         return view('home');
    //     }
    // }

    public function dashboard()
    {
        # code...
        return view('cms.dashboard');
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

    // public function usermgmt()
    // {
    //     # code...
    //     // return User::find(1)->pekerja;
    //     $users = $this->removeWhitespace(DB::table('users')
    //         ->join('EWS_PEKERJA', 'users.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
    //         ->join('EWS_ROLE_USER', 'EWS_PEKERJA.idRole', '=', 'EWS_ROLE_USER.id')
    //         ->select('users.id', 'users.username', 'users.email', 'EWS_PEKERJA.codePekerja', 'EWS_PEKERJA.namaPekerja', 'EWS_ROLE_USER.namaRole', 'EWS_ROLE_USER.descRole')
    //         ->get());

    //     $data['users'] = $users;
    //     return view('cms.usermgmt', $data);
    // }

    // public function reports($dateStart = null, $dateEnd = null)
    // {
    //     # code...
    //     $data['counter'] = 1;
    //     $data['rkm'] = $this->rkmByDate($this->convertDate($dateStart), $this->convertDate($dateEnd));
    //     return view('cms.reports', $data);
    // }

    // public function convertDate($date = null) # 13-02-2019||07-02-2019
    // {
    //     # code...
    //     if ($date == null) {
    //         # code...
    //         return null;
    //     }

    //     $date_crt = date_create($date);
    //     $date_new = date_format($date_crt, 'Y-m-d');
    //     return $date_new;
    // }

    // public function rkmByDate($dateStart = null, $dateEnd = null)
    // {
    //     # code...
    //     $rkm = null;
    //     if (!empty($dateStart)) {
    //         # code...
    //         $rkm = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
    //             ->join('EWS_SUB_JOB', 'EWS_JADWAL_RKM.codeAlojob', '=', 'EWS_SUB_JOB.subJobCode')
    //             ->join('EWS_JOB', 'EWS_JOB.jobCode', '=', 'EWS_SUB_JOB.jobCode')
    //             ->select('EWS_JADWAL_RKM.*', 'EWS_JOB.jobCode as parentJobCode', 'EWS_JOB.Description as parentJobName', 'EWS_SUB_JOB.subJobCode as childJobCode', 'EWS_SUB_JOB.Description as childJobName')
    //             ->whereBetween('EWS_JADWAL_RKM.rkhDate', [$dateStart, $dateStart.' 23:59:59.999'])
    //             ->get());
    //     }

    //     if (!empty($dateEnd)) {
    //         # code...
    //         $rkm = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
    //             ->join('EWS_SUB_JOB', 'EWS_JADWAL_RKM.codeAlojob', '=', 'EWS_SUB_JOB.subJobCode')
    //             ->join('EWS_JOB', 'EWS_JOB.jobCode', '=', 'EWS_SUB_JOB.jobCode')
    //             ->select('EWS_JADWAL_RKM.*', 'EWS_JOB.jobCode as parentJobCode', 'EWS_JOB.Description as parentJobName', 'EWS_SUB_JOB.subJobCode as childJobCode', 'EWS_SUB_JOB.Description as childJobName')
    //             ->whereBetween('EWS_JADWAL_RKM.rkhDate', [$dateStart, $dateEnd.' 23:59:59.999'])
    //             ->get());
    //     }
    //     return $rkm;
    // }
}
