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
        # Berat Tandan
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

    public static function custom($table, $arr = array())
    {
        if ($table == 'BLOK') {
            $query = DB::select('EXEC EWS_sp_blokByDateAndJob @date = ?, @codeAloJob = ?', $arr);
        }

        if ($table == 'DETIL') {
            # code...
            $query = DB::select('EXEC EWS_sp_allPokokStatus @DATE = ?, @AKTIFITAS = ?, @PARENT = ?, @BLOK = ?, @RKH = ?', $arr);
        }

        return collect($query);
    }
}
