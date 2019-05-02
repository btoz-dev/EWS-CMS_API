<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;
use App\Trans;
use App\Exports\MandorExport;
use App\Exports\KawilExport;
use Maatwebsite\Excel\Facades\Excel;

class TransReportController extends CMSController
{
    /**
     * Display a listing of the mandor resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mandorPlantcare(Request $request)
    {
        if ($request->ajax()) {

            $query = Trans::mandor('PLANTCARE');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }

        return view('cms.mandorPlantcareReport');
    }

    public function mandorFruitcare(Request $request)
    {
        if ($request->ajax()) {

            $query = Trans::mandor('FRUITCARE');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }

        return view('cms.mandorFruitcareReport');
    }

    public function mandorPanen(Request $request)
    {
        if ($request->ajax()) {

            $query = Trans::mandor('PANEN');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }

        return view('cms.mandorPanenReport');
    }

    public function exportMandor(Request $request)
    {
    	# code...
    	$export = new MandorExport();
		$export->setHeading($request->heading);
		$export->setJob($request->job);
		$export->setDateAw($request->date_aw);
		$export->setDateAk($request->date_ak);

		return Excel::download($export, 'trans.xlsx');
    }

    /**
     * Display a listing of the kawil resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function kawilPlantcare(Request $request)
    {
        if ($request->ajax()) {

            $query = Trans::kawil('PLANTCARE');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }
        
        return view('cms.kawilPlantcareReport');
    }

    public function kawilFruitcare(Request $request)
    {
        if ($request->ajax()) {
            
            $query = Trans::kawil('FRUITCARE');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }
        
        return view('cms.kawilFruitcareReport');
    }

    public function kawilPanen(Request $request)
    {
        if ($request->ajax()) {
            
            $query = Trans::kawil('PANEN');

            if ($request->date_aw != NULL) {
                # code...
                $query->whereBetween('created_at', [$request->date_aw, $request->date_ak." 23:59:59.000"]);
            }

            $res = $query->get();

            $report = $this->removeWhitespace($res);

            return DataTables::of($report)
                ->make(true);
        }
        
        return view('cms.kawilPanenReport');
    }

    public function exportKawil(Request $request)
    {
    	# code...
    	$export = new KawilExport();
		$export->setHeading($request->heading);
		$export->setJob($request->job);
		$export->setDateAw($request->date_aw);
		$export->setDateAk($request->date_ak);

		return Excel::download($export, 'trans.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
