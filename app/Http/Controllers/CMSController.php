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
        # code...
        ini_set('max_execution_time', '3600');
        // ini_set('max_execution_time', '1');
        ini_set('memory_limit', '10G');
        // ini_set('memory_limit', '100M');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function dashboard()
    {
        # code...
        return view('cms.dashboard');
    }
    
}
