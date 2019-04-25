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
        $fileExist = Storage::disk('public')->exists('apk.log');
        // return var_dump($fileExist);
        if (!$fileExist)
        {
            Storage::put('public/apk.log', 'log apk files');
        }
        // Storage::put('public/apk.log', 'log apk files');

        $data['logTxt'] = Storage::get('public/apk.log');

        return view('cms.insertAPK', $data);
    }

    public function upload(Request $request)
    {
        # code...
        // return $request;
        $path = $request->file('file')->storeAs('/public/apkfile', 'ews.apk');

        Storage::put('public/apk.log', $request->logapk);

        if (!empty($path))
        {
            return redirect()->back()->with('alert', 'File has been uploaded');
        }else{
            return redirect()->back()->with('alert', 'Failed to upload. '.$path);
        }
    }
}
