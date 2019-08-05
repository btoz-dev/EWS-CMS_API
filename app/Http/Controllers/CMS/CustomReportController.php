<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use App\Authorizable;
use App\Trans;
use App\Exports\CustomExport;
use App\Http\Controllers\CMSController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomReportController extends CMSController
{
    use Authorizable;
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

        $data['now'] = $tgl_ubah;

        return view('cms.customReport', $data);
    }

    public function filterByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return $validator->messages()->first();
        }
        $data = DB::select(DB::raw("SELECT * FROM EWS_f_aloJobByDate('".$request->date."') ORDER BY Description"));
        $listData = $this->removeWhitespace($data);
        if (empty($listData)) {
            return "<select><option></option></select>";
        }
        $return = '<select>
                <option value="">Pilih Aktifitas</option>
            </select>';
        foreach($listData as $temp) {
            $temp['Description'] = ucwords(strtolower(rtrim(preg_replace('/- [A-Z]{3}\/[A-Z]{3}/', '', $temp['Description']))));
            $return .= "
            <select>
                <option value='".$temp['codeAlojob']."'>".$temp['Description']."</option>
            </select>
            ";
        }
        
        return $return;
    }

    public function getDetilBlok(Request $request)
    {
        if ($request->ajax()) {
        	if ($request->aktifitas == NULL) {
        		# code...
        		$request->aktifitas = '';
        	}
            $query = Trans::custom('BLOK', array($request->date, $request->aktifitas));
            $res = $this->removeWhitespace($query);

            return DataTables::of($res)
                ->addColumn('aksi', function ($res)
                {
                    $parent_aktifitas = $this->removeWhitespace2(DB::table("EWS_SUB_JOB")->select("jobCode")->where("subJobCode", $res['codeAlojob'])->value("jobCode"));
                    return "
                    <button type='submit' class='btn btn-primary' id='showDetail' 
                    data-date='".$res['rkhDate']."'
                    data-aktifitas='".$res['codeAlojob']."'
                    data-parent='".$parent_aktifitas[0]."'
                    data-blok='".$res['codeBlok']."'
                    data-rkh='".$res['rkhCode']."'
                    data-id='".$res['id']."'
                    >Detail</button>
                    ";
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        return FALSE;
    }

    public function getDetilPokok(Request $request)
    {
        if ($request->ajax()) {

            $query = Trans::custom('DETIL', array($request->date, $request->aktifitas, $request->parent, $request->blok, $request->rkh, $request->id));
            $res = $this->removeWhitespace($query);

            return DataTables::of($res)
        		// ->editColumn('kawilDate', function ($report) {
          //           $tgl = date_create($report['kawilDate']);
          //           $tgl2 = date_format($tgl, 'd M Y');
          //           return $tgl2;
          //       })
                ->make(true);
        }
    }

    public function exportCustom(Request $request)
    {
    	$export = new CustomExport();
		$export->setHeading($request->heading);
		$export->setJob($request->job);
		$export->setArr($request->data);

		return Excel::download($export, 'trans.xlsx');
    }

    // public function postDropdown(Request $request)
    // {
    //     // return $request->all(); # {type: "aktifitas", id: "RKH/KL01/0119/0029"}
    //     $set = $request->id;

    //     # Buat pilihan "Switch Case" berdasarkan variabel "type" dari form
    //     switch($request->type):
    //         case 'rkh':
    //             $data = DB::table('EWS_JADWAL_RKM')
    //                 ->distinct()
    //                 ->select('rkhCode')
    //                 ->whereBetween('rkhDate', [$request->dateAwal, $request->dateAkhir])
    //                 ->orderBy('rkhCode', 'asc')
    //                 ->get();
    //             $listData = $this->removeWhitespace($data);
    //             $return = '<option value="">Pilih RKH...</option>';
    //             foreach($listData as $temp) 
    //                 $return .= "<option value=".$temp['rkhCode'].">".$temp['rkhCode']."</option>";
    //             return $return;
    //         break;
    //         case 'aktifitas':
    //             $data = DB::table('EWS_JADWAL_RKM')
    //                 ->distinct()
    //                 ->join('EWS_SUB_JOB', 'EWS_SUB_JOB.subJobCode', '=', 'EWS_JADWAL_RKM.codeAlojob')
    //                 ->select('EWS_JADWAL_RKM.codeAlojob', 'EWS_SUB_JOB.Description')
    //                 ->where('rkhCode', '=', $set)
    //                 ->orderBy('codeAlojob', 'asc')
    //                 ->get();
    //             $listData = $this->removeWhitespace($data);
    //             $return = '<option value="">Pilih Aktifitas...</option>';
    //             foreach($listData as $temp) 
    //                 $return .= "<option value=".$temp['codeAlojob'].">".$temp['Description']."</option>";
    //             return $return;
    //         break;
    //         case 'blok':
    //             $data = DB::table('EWS_JADWAL_RKM')
    //                 ->distinct()
    //                 ->select('codeBlok')
    //                 ->where('codeAlojob', '=', $set)
    //                 ->orderBy('codeBlok', 'asc')
    //                 ->get();
    //             $listData = $this->removeWhitespace($data);
    //             $return = '<option value="">Pilih Blok...</option>';
    //             foreach($listData as $temp) 
    //                 $return .= "<option value=".$temp['codeBlok'].">".$temp['codeBlok']."</option>";
    //             return $return;
    //         break;
    //     endswitch;
    // }

    // public function postFilter(Request $request)
    // {
    //     # code...
    //     // return $request->all();
    //     $table = DB::select('exec EWS_sp_allPokokStatus @rkhCode = ?, @codeAlojob = ?, @codeBlok = ?',array($request->rkhCode,$request->codeAlojob,$request->codeBlok));

    //     return $table;
    // }

    // public function chartDataSet(Request $request)
    // {
    //     # code...
    //     // $table = DB::select('exec EWS_sp_allPokokStatus @rkhCode = ?, @codeAlojob = ?, @codeBlok = ?',array($request->rkhCode,$request->codeAlojob,$request->codeBlok));
    //     $query = DB::table('EWS_JADWAL_RKM')
    //         ->select('*')
    //         ->selectRaw('convert(varchar, rkhDate, 106) as tanggal')
    //         ->selectRaw('dbo.EWS_f_getTotalPokok(codeBlok, barisStart, barisEnd) as totalPokok')
    //         ->selectRaw('dbo.EWS_f_totalPokokDone(rkhCode, codeAlojob, codeBlok) as pokokDone')
    //         ->selectRaw('dbo.EWS_f_realisasiPersen(dbo.EWS_f_getTotalPokok(codeBlok, barisStart, barisEnd), dbo.EWS_f_totalPokokDone(rkhCode, codeAlojob, codeBlok)) as persentase')
    //         ->where('rkhCode', '=', $request->rkhCode)
    //         ->where('codeAlojob', '=', $request->codeAlojob)
    //         ->where('codeBlok', '=', $request->codeBlok)
    //         ->first();
    //         $report = $this->removeWhitespace2($query);
    //     // $table = DB::select('exec EWS_sp_allPokokStatus @rkhCode = ?, @codeAlojob = ?, @codeBlok = ?',array('RKH/KL01/0319/0292', '5210400100', '2031-R0'));

    //     return $report;
    // }
}
