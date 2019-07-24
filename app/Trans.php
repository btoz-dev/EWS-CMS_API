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
	                ->selectRaw('convert(CHAR(17), created_at, 113) as created_at2, convert(CHAR(11), rkhDate, 113) as rkhDate2');

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
	                ->selectRaw('convert(CHAR(17), created_at, 113) as created_at, convert(CHAR(11), rkhDate, 113) as rkhDate');

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
        # Tandan - Bonggol
        if ($job == 'TB') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_TB_TRANS');
        }

        # Berat Tandan
        if ($job == 'BT') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_BT_TRANS');
        }

        # Berat Bonggol
        if ($job == 'BB') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_BB_TRANS');
        }

        if ($job == 'HT') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_QC_TRANS');
        }
        
        if ($job == 'CLT') {
            # code...
            $query = DB::table('EWS_VW_CMS_PH_CLT_TRANS');
        }

        return $query;
    }

    public static function spi($job)
    {
        # Tandan - Bonggol
        if ($job == 'MANDOR') {
            # code...
            $query = DB::table('EWS_VW_CMS_SPI_MANDOR');
        }

        # Berat Tandan
        if ($job == 'SENSUS') {
            # code...
            $query = DB::table('EWS_VW_CMS_SPI_SENSUS');
        }

        return $query;
    }

    public static function custom($table, $arr = array())
    {
        if ($table == 'BLOK') {
            $query = DB::select('EXEC EWS_sp_blokByDateAndJob @date = ?, @codeAloJob = ?', $arr);
        }

        if ($table == 'DETIL') {
            # code...
            $query = DB::select('EXEC EWS_sp_allPokokStatus @DATE = ?, @AKTIFITAS = ?, @PARENT = ?, @BLOK = ?, @RKH = ?, @ID = ?', $arr);
        }

        return collect($query);
    }
}
