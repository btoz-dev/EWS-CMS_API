<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Trans extends Model
{
    //
    public static function mandor($job)
    {
    	# code...
    	$query = DB::table('EWS_VW_CMS_MANDOR_TRANS')
	                ->select('*')
	                ->selectRaw('convert(CHAR(17), created_at, 113) as created_at');

    	if ($job == 'PLANTCARE') {
    		# code...
    		$query->where('jobCode', '=', '001');
    	}

    	if ($job == 'FRUITCARE') {
    		# code...
    		$query->where('jobCode', '=', '002');
		}
		
		if ($job == 'PANEN') {
    		# code...
    		$query->where('jobCode', '=', '003');
    	}

    	return $query;
    }

    public static function kawil($job)
    {
    	# code...
    	$query = DB::table('EWS_VW_CMS_KAWIL_TRANS')
	                ->select('*')
	                ->selectRaw('convert(CHAR(17), created_at, 113) as created_at');

    	if ($job == 'PLANTCARE') {
    		# code...
    		$query->where('jobCode', '=', '001');
    	}

    	if ($job == 'FRUITCARE') {
    		# code...
    		$query->where('jobCode', '=', '002');
		}
		
		if ($job == 'PANEN') {
    		# code...
    		$query->where('jobCode', '=', '003');
    	}

    	return $query;
	}
	
	public static function ph($job)
    {
        # code...
        if ($job == 'BT') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_BT_TRANS');
        }

        if ($job == 'HT') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_HT_TRANS');
        }
        
        if ($job == 'CLT') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_CLT_TRANS');
        }

        return $query;
    }

    public static function rkm()
    {
        # code...
        $query = DB::table('EWS_JADWAL_RKM')
            ->select('EWS_JADWAL_RKM.id', 'EWS_JADWAL_RKM.rkhCode', 'EWS_VW_DETAIL_MANDOR.namaPekerja', 'EWS_SUB_JOB.Description', 'EWS_JADWAL_RKM.codeBlok', 'EWS_JADWAL_RKM.barisStart', 'EWS_JADWAL_RKM.barisEnd', 'EWS_JADWAL_RKM.rkhDate')
            ->selectRaw('convert(varchar, EWS_JADWAL_RKM.rkhDate, 106) as tanggal')
            ->selectRaw('dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd) as totalPokok')
            ->selectRaw('dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok) as pokokDone')
            ->selectRaw('dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd) - dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok) as pokokNDone')
            ->selectRaw('dbo.EWS_f_realisasiPersen(dbo.EWS_f_getTotalPokok(EWS_JADWAL_RKM.codeBlok, EWS_JADWAL_RKM.barisStart, EWS_JADWAL_RKM.barisEnd), dbo.EWS_f_totalPokokDone(EWS_JADWAL_RKM.rkhCode, EWS_JADWAL_RKM.codeAlojob, EWS_JADWAL_RKM.codeBlok)) as persentase')
            ->join('EWS_VW_DETAIL_MANDOR', 'EWS_VW_DETAIL_MANDOR.codeMandor', '=', 'EWS_JADWAL_RKM.mandorCode')
            ->join('EWS_SUB_JOB', 'EWS_SUB_JOB.subJobCode', '=', 'EWS_JADWAL_RKM.codeAlojob');
    }

}
