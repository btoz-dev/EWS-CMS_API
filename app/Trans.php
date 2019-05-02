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
}
