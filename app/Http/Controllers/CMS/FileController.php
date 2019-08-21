<?php

namespace App\Http\Controllers\CMS;

use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;
use Illuminate\Support\Facades\Storage;

class FileController extends CMSController
{
    //
    public function index()
    {
        $fileRKHExist = Storage::disk('public')->exists('apk_rkh.log');
        $filePHExist = Storage::disk('public')->exists('apk_ph.log');
        if (!$fileRKHExist)
        {
            Storage::put('public/apk_rkh.log', 'log apk files');
        }
        if (!$filePHExist)
        {
            Storage::put('public/apk_ph.log', 'log apk files');
        }
        // Storage::put('public/apk.log', 'log apk files');

        $data['logTxtRKH'] = Storage::get('public/apk_rkh.log');
        $data['logTxtPH'] = Storage::get('public/apk_ph.log');

        return view('cms.insertAPK', $data);
    }

    public function uploadApkRKH(Request $request)
    {
        # code...
        $path = $request->file('file')->storeAs('/public/apkfile', 'ews_rkh.apk');

        Storage::put('public/apk_rkh.log', $request->logapk);

        if (!empty($path))
        {
            return redirect()->back()->with('alertRKH', 'File has been uploaded');
        }else{
            return redirect()->back()->with('alertRKH', 'Failed to upload. '.$path);
        }
    }

    public function uploadApkPH(Request $request)
    {
        # code...
        $path = $request->file('file')->storeAs('/public/apkfile', 'ews_ph.apk');

        Storage::put('public/apk_ph.log', $request->logapk);

        if (!empty($path))
        {
            return redirect()->back()->with('alertPH', 'File has been uploaded');
        }else{
            return redirect()->back()->with('alertPH', 'Failed to upload. '.$path);
        }
    }
}
