<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const ID_ROLE_ADMIN = 1;
    const ID_ROLE_SUPER_ADMIN = 1;
    const ID_ROLE_MANAGEMENT = 2;
    const ID_ROLE_SPI = 3;
    const ID_ROLE_PH_BERAT_BONGGOL = 4;
    const ID_ROLE_PH_BERAT_BRUTO = 5;
    const ID_ROLE_RANGKAP = 6;
    const ID_ROLE_KAWIL = 7;
    const ID_ROLE_MANDOR = 8;
    
    public function removeWhitespace($arr)
    {
        if (!empty($arr)) {
            $arr = json_decode(json_encode($arr),TRUE);
            foreach ($arr as $key => $value) {
                $arr[$key] = array_map('rtrim',$arr[$key]);
            }
            return $arr;
        } else {
            return false;
        }
    }

    public function removeWhitespace2($arr)
    {
        if (!empty($arr)) {
            $arr = (array) $arr;
            $arr = array_map('rtrim',$arr);

            return $arr;
        } else {
            return false;
        }
    }
}
