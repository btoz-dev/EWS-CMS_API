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

}
