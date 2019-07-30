<?php

namespace App\Http\Controllers\Api\Dev;

use DB;
use App\User;
use App\Roles;
use App\Pekerja;
use App\Permission;
use App\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class DevController extends Controller
{
    public function __construct()
    {
    	# code...
    	ini_set('max_execution_time', '3600');
	    ini_set('memory_limit', '1G');
    }
    
	public function test(Request $request)
	{
		# code...
		return 'INI DARI API DEV || DB => '.DB::getDatabaseName();
		// return var_dump(Artisan::call('config:cache'));
	}

    public function getUser (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errMessage(400,$validator->messages()->first());
        }

        $user2 = $this->removeWhitespace3(
                User::select(['id','name','username','password_decrypt as password','codePekerja'])
                ->whereRaw('username = ? COLLATE Latin1_General_CS_AS', [$request->username])
                ->whereRaw('password_decrypt = ? COLLATE Latin1_General_CS_AS', [$request->password])
                ->first()
            );
        if (empty($user2)) {
            # code...
            return $this->errMessage(400,'Username atau Password salah.');
        }
        
        $identitasPekerja = $this->removeWhitespace3(
            Pekerja::select(['namaPekerja as nama'])->find($user2['codePekerja'])
        );
        if (empty($identitasPekerja)) {
            # code...
            return $this->errMessage(400,'Tidak ada data pekerja');
        }

        $detailRole = User::find($user2['id'])->roles->first();
        if (empty($detailRole)) {
            # code...
            return $this->errMessage(400,'Tidak ada data role');
        }
        
        $detailRole['desc'] = $detailRole['name'];
        unset($detailRole['guard_name']);
        unset($detailRole['created_at']);
        unset($detailRole['updated_at']);
        unset($detailRole['pivot']);
        $user[0] = $user2;
        $user2 = $user;

        if ($detailRole['name'] == "Mandor") {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date|date_format:d-m-Y'
            ]);
            if ($validator->fails()) {
                return $this->errMessage(400,$validator->messages()->first());
            }
            return $this->getRKMMandor($user2, $identitasPekerja, $detailRole, $request->date);
        }

        else if ($detailRole['name'] == "Kawil") {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date|date_format:d-m-Y'
            ]);
            if ($validator->fails()) {
                return $this->errMessage(400,$validator->messages()->first());
            }
            return $this->getRKMKawil($user2, $identitasPekerja, $detailRole, $request->date);
        }

        else if ($detailRole['name'] == "Mandor PH") {
            $validator = Validator::make($request->all(), [
                'data' => [
                    'required', 
                    Rule::in(['tandan', 'bonggol', 'qc', 'clt'])
                ],
            ]);
            if ($validator->fails()) {
                return $this->errMessage(400,$validator->messages()->first());
            }
            $detailRole['nama'] = $detailRole['name'];
            unset($detailRole['name']);
            return $this->getPH($user2, $identitasPekerja, $detailRole, $request->data);
        }

        else if ($detailRole['name'] == "SPI") {
            return $this->getSPI($user2, $identitasPekerja, $detailRole, $request);
        }

        else {
            return $this->errMessage(400,'User tidak memiliki Hak');
        }
    }

    # MANDOR KAWIL PLANTCARE-FRUITCARE-PANEN #
	    public function getRKMMandor ($user2, $identitasPekerja, $detailRole, $reqDate)
        {
            // $date = now();
            $tgl = date_create($reqDate);

            $codeMandor = $this->removeWhitespace2(DB::table('EWS_MANDOR')
                ->select('codeMandor')
                ->where('codePekerja', '=', $user2[0]['codePekerja'])
                ->first());
            if (empty($codeMandor)) {
                # code...
                return $this->errMessage(400,'Tidak ada data kode mandor');
            }

            $bln_tukang = date_format($tgl, 'm');
            $thn_tukang = date_format($tgl, 'Y');

            $tukang = $this->removeWhitespace(DB::table('EWS_PEKERJA')
                ->join('EWS_MANDOR_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR_PEKERJA.codePekerja')
                ->select('EWS_PEKERJA.namaPekerja as nama', 'EWS_PEKERJA.codePekerja as code')
                ->where('EWS_MANDOR_PEKERJA.codeMandor', '=', $codeMandor['codeMandor'])
                ->where('EWS_MANDOR_PEKERJA.AccMonth', '=', $bln_tukang)
                ->where('EWS_MANDOR_PEKERJA.AccYear', '=', $thn_tukang)
                ->orderBy('nama', 'asc')
                ->get());
            if (empty($tukang)) {
                # code...
                return $this->errMessage(400,'Tidak ada data tukang');
            }

            $tgl_ubah = date_format($tgl, 'Y-m-d');
            $user2[0]['rkhDate'] = $tgl_ubah;

            unset($identitasPekerja['idRole']);
            $user2[0]['identitasPekerja'] = $identitasPekerja;
            $user2[0]['identitasPekerja']['detailRole'] = $detailRole;
            $user2[0]['identitasPekerja']['detailPekerja'] = $codeMandor;

            $pilihTukang = array(
                'id' => '',
                'nama' => 'Pilih Pekerja',
                'code' => ''
            );
            array_unshift($tukang, $pilihTukang); #inserts new elements into beginning of array
            $user2[0]['identitasPekerja']['detailPekerja']['tukang'] = $tukang;

            ####################################GET ALL DATA###################################################

            $user2[0]['RKM'] = array();
            # rencana kerjaan harian
            $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
                ->join('EWS_SUB_JOB', 'EWS_JADWAL_RKM.codeAlojob', '=', 'EWS_SUB_JOB.subJobCode')
                ->join('EWS_JOB', 'EWS_JOB.jobCode', '=', 'EWS_SUB_JOB.jobCode')
                ->select('EWS_JADWAL_RKM.*', 'EWS_JOB.jobCode as parentJobCode', 'EWS_JOB.Description as parentJobName', 'EWS_SUB_JOB.subJobCode as childJobCode', 'EWS_SUB_JOB.Description as childJobName')
                ->whereBetween('EWS_JADWAL_RKM.rkhDate', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
                ->where('EWS_JADWAL_RKM.mandorCode', '=', $codeMandor['codeMandor'])
                ->get());
            if (empty($rkm2)) {
                # code...
                return $this->errMessage(400,'Tidak ada RKM untuk tanggal '.date_format($tgl, 'd-m-Y'));
            }
            foreach ($rkm2 as $key_rkm => $rkm) {
                # code...
                $date = date_create($rkm['rkhDate']);
                unset($rkm2[$key_rkm]['rkhDate']);
                # tanggal bulan tahun
                $rkm2[$key_rkm]['rkhDate'] = date_format($date, 'd F Y');
                # jam:menit:detik
                $rkm2[$key_rkm]['rkhTime'] = date_format($date, 'H:i:s');

                $rkmListBlok[] = str_replace('.', '-', $rkm['codeBlok']);
                $rkmListBlok = array_unique($rkmListBlok);
                $rkmListBlok = array_values($rkmListBlok);

                $rkmListSubJob[] = str_replace('.', '-', $rkm['childJobCode']);
                $rkmListSubJob = array_unique($rkmListSubJob);
                $rkmListSubJob = array_values($rkmListSubJob);
            }

            $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());
            if (empty($job2)) {
                # code...
                return $this->errMessage(400,'Aktifitas tidak ditemukan');
            }

            $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->whereIn('subJobCode', $rkmListSubJob)->get());
            if (empty($subJob2)) {
                # code...
                return $this->errMessage(400,'Sub-Aktifitas tidak ditemukan');
            }
            foreach ($subJob2 as $key => $value) {
                # code...
                $subJob2[$key]['Description'] = rtrim(preg_replace('/- [A-Z]{3}\/[A-Z]{3}/', '', $value['Description']));
            }

            $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
                ->select('codeBlok')
                ->distinct('codeBlok')
                ->whereIn('codeBlok', $rkmListBlok)
                ->orderBy('codeBlok', 'asc')
                ->get());
            if (empty($listBlok2)) {
                # code...
                return $this->errMessage(400,'List blok tidak ditemukan');
            }

            $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
                ->select('codeBlok', 'plot')
                ->distinct('codeBlok')
                ->whereIn('codeBlok', $rkmListBlok)
                ->orderBy('plot', 'asc')
                ->get());
            if (empty($listPlot2)) {
                # code...
                return $this->errMessage(400,'List plot tidak ditemukan');
            }

            $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
                ->select('codeBlok','plot', 'baris')
                ->distinct('baris')
                ->whereIn('codeBlok', $rkmListBlok)
                ->orderBy('baris', 'asc')
                ->get());
            if (empty($listBaris2)) {
                # code...
                return $this->errMessage(400,'List baris tidak ditemukan');
            }

            $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
                ->selectRaw('codeTanaman as code, codeBlok, plot, baris, noTanam, PlantingDate, DATEDIFF(wk, PlantingDate,GETDATE()) as week, 0 as status')
                ->whereIn('codeBlok', $rkmListBlok)
                ->orderBy('codeTanaman', 'asc')
                ->get());
            if (empty($listPokok2)) {
                # code...
                return $this->errMessage(400,'List pokok tidak ditemukan');
            }

            $trans_mandor2 = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
                ->select('id', 'subJobCode', 'codeTanaman', 'rkhCode')
                ->where('userid', '=', $user2[0]['id'])
                // ->whereBetween('created_at', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
                ->get());

            $newRKM = array();
            foreach ($rkm2 as $key => $value) {
                # code...
                $newList = array(
                    'rkhCode' => $value['rkhCode'],
                    'blok' => $value['codeBlok'],
                    'rowStart' => $value['barisStart'],
                    'rowEnd' => $value['barisEnd']
                );
                $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['parentJobCode'] = $value['parentJobCode'];
                $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['parentJobName'] = $value['parentJobName'];
                $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['childJobCode'] = $value['childJobCode'];
                $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['childJobName'] = $value['childJobName'];
                $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['listBlok'][] = $newList;
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhCode'] = $value['rkhCode'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['mandorCode'] = $value['mandorCode'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['codeAlojob'] = $value['codeAlojob'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobCode'] = $value['parentJobCode'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobName'] = $value['parentJobName'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobCode'] = $value['childJobCode'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobName'] = $value['childJobName'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhDate'] = $value['rkhDate'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhTime'] = $value['rkhTime'];
                // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['listBlok'][] = $newList;
            }
            $rkm2 = $newRKM;

            foreach ($rkm2 as $key_rkm => $rkm) {
                # code...
                foreach ($subJob2 as $key_sj => $subJob) {
                    # code...
                    if (isset($rkm['childJobCode'])) {
                        # code...
                        if ($subJob['subJobCode'] == $rkm['childJobCode']) {
                            # code...
                            unset($rkm['parentJobCode']);
                            unset($rkm['parentJobName']);
                            unset($rkm['childJobCode']);
                            unset($rkm['childJobName']);
                            // unset($rkm['codeBlok']);
                            // unset($rkm['rowStart']);
                            // unset($rkm['rowEnd']);
                            // array_push($subJob2[$key_sj], $rkm);
                            $subJob2[$key_sj] = array_merge($subJob,$rkm);
                        }
                    }
                }
            }

            # MASUKIN PLOT-BARIS-POKOK ke dalam BLOK
            for ($a=0; $a < count($listBlok2); $a++) { 
                $dataBlok[$a] = $listBlok2[$a];
                for ($b=0; $b < count($listPlot2); $b++) {
                    if ($dataBlok[$a]['codeBlok'] == $listPlot2[$b]['codeBlok']) {
                        $dataBlok[$a]['listPlot'][$b] = $listPlot2[$b];
                        for ($c=0; $c < count($listBaris2); $c++) { 
                            if  (($dataBlok[$a]['listPlot'][$b]['plot'] == $listBaris2[$c]['plot']) && 
                                ($dataBlok[$a]['listPlot'][$b]['codeBlok'] == $listBaris2[$c]['codeBlok'])) {
                                $dataBlok[$a]['listPlot'][$b]['listBaris'][$c] = $listBaris2[$c];
                                for ($d=0; $d < count($listPokok2); $d++) { 
                                    if  (($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['codeBlok'] == $listPokok2[$d]['codeBlok']) &&
                                        ($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['plot'] == $listPokok2[$d]['plot']) && 
                                        ($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['baris'] == $listPokok2[$d]['baris'])) {
                                        $date = date_create($listPokok2[$d]['PlantingDate']);
                                        $listPokok2[$d]['date'] = date_format($date, 'd F Y');
                                        unset($listPokok2[$d]['PlantingDate']);
                                        unset($listPokok2[$d]['date']);
                                        $dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'][$d] = $listPokok2[$d];

                      //                $allPokokPlot = $listPokok2[$d];
                      //                unset($allPokokPlot['codeBlok']);
                                        // unset($allPokokPlot['plot']);
                                        // unset($allPokokPlot['baris']);
                                        // unset($allPokokPlot['noTanam']);
                                        // unset($allPokokPlot['PlantingDate']);
                                        // unset($allPokokPlot['week']);
                                        // unset($allPokokPlot['status']);
                                        // unset($allPokokPlot['date']);
                      //                $dataBlok[$a]['listPlot'][$b]['listAllPokokPlot'][] = $allPokokPlot;
                                    }
                                }
                                $dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok']);
                            }
                        }
                        $dataBlok[$a]['listPlot'][$b]['listBaris'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris']);
                    } 
                }
                $dataBlok[$a]['listPlot'] = array_values($dataBlok[$a]['listPlot']);
            }

            # MASUKIN PLOT-BARIS-POKOK
            foreach ($subJob2 as $key_sj => $subJob) { #Plot
                # code...
                foreach ($subJob['listBlok'] as $key_lb => $listBlok) {
                    # code...
                    foreach ($dataBlok as $key_db => $db) {
                        # code...
                        if ($listBlok['blok'] == $db['codeBlok']) {
                            # code...
                            if (($listBlok['rowStart'] == 0) && ($listBlok['rowEnd'] == 0)) {
                                # code...
                                // $subJob2[$key_sj]['listBlok'][$key_lb]['listPlot'] = $db['listPlot'];
                                $listPlot = $db['listPlot'];
                            } else {
                                foreach ($db['listPlot'] as $key_plot => $plot) {
                                    # code...
                                    foreach ($plot['listBaris'] as $key_baris => $baris) {
                                        # code...
                                        if ($baris['baris'] >= $listBlok['rowStart']  && $baris['baris'] <= $listBlok['rowEnd']) {
                                            # do nothing
                                        }else {
                                            unset($dataBlok[$key_db]['listPlot'][$key_plot]['listBaris'][$key_baris]);
                                        }
                                    }
                                    $listPlot[] = $dataBlok[$key_db]['listPlot'][$key_plot];
                                }
                            }
                            $lpDone = 0;
                            $lpNDone = 0;
                            foreach ($listPlot as $key_plot => $plot) {
                                # code...
                                $lbDone = 0;
                                $lbNDone = 0;
                                foreach ($plot['listBaris'] as $key_baris => $baris) {
                                    # code...
                                    $lpkDone = 0;
                                    $lpkNDone = 0;
                                    foreach ($baris['listPokok'] as $key_pokok => $pokok) {
                                        # code...
                                        foreach ($trans_mandor2 as $key_tm => $trans_spi) {
                                            # code...
                                            if (($trans_spi['rkhCode'] == $listBlok['rkhCode']) && 
                                            ($trans_spi['codeTanaman'] == $pokok['code']) &&
                                            ($trans_spi['subJobCode'] == $subJob['subJobCode'])) {
                                                # code...
                                                // return 'ada';
                                                // $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][$key_lpk]['status'] = 1;
                                                $listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['status'] = 1;
                                            }
                                        }
                                        if ($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['status'] == 1) {
                                            # code...
                                            $lpkDone++;
                                        }else {
                                            $lpkNDone++;
                                        }
                                        unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['week']);
                                        unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['codeBlok']);
                                        unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['plot']);
                                        unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['baris']);
                                        unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['noTanam']);
                                        // $allPokokPlot = $listPokok2[$d];
                                        unset($pokok['codeBlok']);
                                        unset($pokok['plot']);
                                        unset($pokok['baris']);
                                        unset($pokok['noTanam']);
                                        unset($pokok['PlantingDate']);
                                        unset($pokok['status']);
                                        unset($pokok['date']);
                                        $listPlot[$key_plot]['listAllPokokPlot'][] = $pokok;
                                    }
                                    unset($listPlot[$key_plot]['listBaris'][$key_baris]['codeBlok']);
                                    unset($listPlot[$key_plot]['listBaris'][$key_baris]['plot']);

                                    $listPlot[$key_plot]['listBaris'][$key_baris]['pokokDone'] = $lpkDone;
                                    $listPlot[$key_plot]['listBaris'][$key_baris]['pokokNDone'] = $lpkNDone;
                                    $this->move_to_top($listPlot[$key_plot]['listBaris'][$key_baris], 'pokokNDone');
                                    $this->move_to_top($listPlot[$key_plot]['listBaris'][$key_baris], 'pokokDone');
                                    $this->move_to_top($listPlot[$key_plot]['listBaris'][$key_baris], 'baris');

                                    $lbDone+=$lpkDone;
                                    $lbNDone+=$lpkNDone;
                                }
                                unset($listPlot[$key_plot]['codeBlok']);

                                $listPlot[$key_plot]['rowDone'] = $lbDone;
                                $listPlot[$key_plot]['rowNDone'] = $lbNDone;
                                $this->move_to_top($listPlot[$key_plot], 'rowNDone');
                                $this->move_to_top($listPlot[$key_plot], 'rowDone');
                                $this->move_to_top($listPlot[$key_plot], 'plot');
                                $subJob2[$key_sj]['listBlok'][$key_lb]['listPlot'] = $listPlot;

                                $lpDone+=$lbDone;
                                $lpNDone+=$lbNDone;
                            }
                            $subJob2[$key_sj]['listBlok'][$key_lb]['plotDone'] = $lpDone;
                            $subJob2[$key_sj]['listBlok'][$key_lb]['plotNDone'] = $lpNDone;
                            $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lb], 'plotNDone');
                            $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lb], 'plotDone');
                            $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lb], 'blok');
                            unset($listPlot);
                        }
                    }
                }
            }
            # MASUKIN BLOK-PLOT-BARIS-TANAM

            foreach ($job2 as $key_j => $job) {
                # code...
                foreach ($subJob2 as $key_sj => $subJob) {
                    # code...
                    $parentJob = '';
                    switch ($job['Description']) {
                        case 'PLANT CARE':
                            $parentJob = 'plantCare';
                            break;

                        case 'FRUIT CARE':
                            $parentJob = 'fruitCare';
                            break;

                        case 'PANEN':
                            $parentJob = 'panen';
                            break;
                        case 'PACKING HOUSE':
                            $parentJob = 'packingHouse';
                            break;
                    }
                    $job2[$key_j][$parentJob]['jobCode'] = $job['jobCode'];
                    $jobdesc = ucwords(strtolower($job['Description']));
                    $job2[$key_j][$parentJob]['jenisPekerjaan'] = $jobdesc;
                    if ($subJob['jobCode'] == $job['jobCode']) {
                        # code...
                        unset($subJob['codeAlojob']);
                        unset($subJob['rkhTime']);
                        $job2[$key_j][$parentJob]['listChildJob'][] = $subJob;
                    }
                }
                unset($job2[3]); # menghapus packing house
                unset($job2[$key_j]['jobCode']);
                unset($job2[$key_j]['Description']);
            }

            $user2[0]['RKM'] = $job2;

            return $user2;
        }

	    public function getRKMKawil ($user2, $identitasPekerja, $detailRole, $reqDate)
	    {
	        // $date = now();
	        // $date = '22-03-2019';
	        $tgl = date_create($reqDate);
	        $tgl_ubah = date_format($tgl, 'Y-m-d');

	        $user2[0]['rkhDate'] = $tgl_ubah;

	        unset($identitasPekerja['idRole']);
	        $user2[0]['identitasPekerja'] = $identitasPekerja;
	        $user2[0]['identitasPekerja']['detailRole'] = $detailRole;

	        ####################################GET ALL DATA###################################################
	        
	        $user2[0]['RKM'] = array();
	        // # rencana kerjaan harian
	        $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
	            ->join('EWS_TRANS_SPI_MANDOR', function ($join) {
	                $join->on('EWS_TRANS_SPI_MANDOR.rkhCode', '=', 'EWS_JADWAL_RKM.rkhCode');
	                $join->on('EWS_TRANS_SPI_MANDOR.codeBlok', '=', 'EWS_JADWAL_RKM.codeBlok');
	                $join->on('EWS_TRANS_SPI_MANDOR.subJobCode', '=', 'EWS_JADWAL_RKM.codeAlojob');
	            })
	            ->join('EWS_SUB_JOB', 'EWS_JADWAL_RKM.codeAlojob', '=', 'EWS_SUB_JOB.subJobCode')
	            ->join('EWS_JOB', 'EWS_JOB.jobCode', '=', 'EWS_SUB_JOB.jobCode')
	            ->join('EWS_MANDOR', 'EWS_MANDOR.codeMandor', '=', 'EWS_JADWAL_RKM.mandorCode')
	            ->join('EWS_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR.codePekerja')
	            // ->select('EWS_JADWAL_RKM.*')
	            ->select('EWS_JADWAL_RKM.*', 'EWS_JOB.jobCode as parentJobCode', 'EWS_JOB.Description as parentJobName', 'EWS_SUB_JOB.subJobCode as childJobCode', 'EWS_SUB_JOB.Description as childJobName', 'EWS_PEKERJA.namaPekerja as namaMandor')
	            ->whereBetween('EWS_TRANS_SPI_MANDOR.created_at', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
	            ->distinct()
	            ->get());
	        if (empty($rkm2)) {
	            # code...
	            return $this->errMessage(400,'Tidak ada RKM untuk hari ini');
	        }

	        $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());
	        $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->get());
	        foreach ($subJob2 as $key => $value) {
	            # code...
	            $subJob2[$key]['Description'] = rtrim(preg_replace('/- [A-Z]{3}\/[A-Z]{3}/', '', $value['Description']));
	        }

	        foreach ($rkm2 as $key_rkm => $rkm) {
	            # code...
	            $date = date_create($rkm['rkhDate']);
	            unset($rkm2[$key_rkm]['rkhDate']);
	            # tanggal bulan tahun
	            $rkm2[$key_rkm]['rkhDate'] = date_format($date, 'd F Y');
	            # jam:menit:detik
	            $rkm2[$key_rkm]['rkhTime'] = date_format($date, 'H:i:s');
	        }

	        $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok')
	            ->distinct('codeBlok')
	            ->orderBy('codeBlok', 'asc')
	            ->get());
	        $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok', 'plot')
	            ->distinct('codeBlok')
	            ->orderBy('plot', 'asc')
	            ->get());
	        $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok','plot', 'baris')
	            ->distinct('baris')
	            ->orderBy('baris', 'asc')
	            ->get());
	        // $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	        //     ->orderBy('codeTanaman', 'asc')
	        //     ->get());
	        $listPokok2 = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_MANDOR as a')
	            ->join('EWS_LOK_TANAMAN as b', 'a.codeTanaman', '=', 'b.codeTanaman')
	            // ->join('users as c', 'a.userid', '=', 'c.id')
	            ->join('EWS_MANDOR as d', 'a.codeMandor', '=', 'd.codeMandor')
	            ->join('EWS_PEKERJA as e', 'a.codeTukang', '=', 'e.codePekerja') # nama Tukang
	            ->join('EWS_PEKERJA as f', 'd.codePekerja', '=', 'f.codePekerja') # nama Mandor
	            ->select('a.rkhCode', 'a.subJobCode', 'd.codeMandor', 'f.namaPekerja as mandor', 'a.codeTukang', 'e.namaPekerja as tk', 'a.created_at' , 'b.id','b.codeTanaman', 'b.codeBlok', 'b.plot', 'b.baris', 'b.noTanam', 'b.PlantingDate', 'a.totalHand', 'a.totalFinger', 'a.totalLeaf', 'a.ribbonColor', 'a.skimmingSize', 'a.id as idTransMandor')
	            ->orderBy('a.codeTanaman', 'asc')
	            ->get());

	        $newRKM = array();
	        foreach ($rkm2 as $key => $value) {
	            # code...
	            $newList = array(
	                'rkhCode' => $value['rkhCode'],
	                'rkhDate' => $value['rkhDate'],
	                'mandorCode' => $value['mandorCode'],
	                'blok' => $value['codeBlok'],
	                'rowStart' => $value['barisStart'],
	                'rowEnd' => $value['barisEnd']
	            );
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['parentJobCode'] = $value['parentJobCode'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['parentJobName'] = $value['parentJobName'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['childJobCode'] = $value['childJobCode'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['childJobName'] = $value['childJobName'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['listBlok'][] = $newList;
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhCode'] = $value['rkhCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['mandorCode'] = $value['mandorCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['codeAlojob'] = $value['codeAlojob'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobCode'] = $value['parentJobCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobName'] = $value['parentJobName'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobCode'] = $value['childJobCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobName'] = $value['childJobName'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhDate'] = $value['rkhDate'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhTime'] = $value['rkhTime'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['listBlok'][] = $newList;
	        }
	        $rkm2 = $newRKM;

	        foreach ($rkm2 as $key_rkm => $rkm) {
	            # code...
	            foreach ($subJob2 as $key_sj => $subJob) {
	                # code...
	                if (isset($rkm['childJobCode'])) {
	                    # code...
	                    if ($subJob['subJobCode'] == $rkm['childJobCode']) {
	                        # code...
	                        unset($rkm['parentJobCode']);
	                        unset($rkm['parentJobName']);
	                        unset($rkm['childJobCode']);
	                        unset($rkm['childJobName']);
	                        // unset($rkm['codeBlok']);
	                        // unset($rkm['rowStart']);
	                        // unset($rkm['rowEnd']);
	                        // array_push($subJob2[$key_sj], $rkm);
	                        $subJob2[$key_sj] = array_merge($subJob,$rkm);
	                    }
	                }
	            }
	        }

	        foreach ($subJob2 as $key_sj => $subJob) { # hapus subJob tak dipakai
	            # code...
	            foreach ($listBlok2 as $key_lb => $listBlok) {
	                # code...
	                if (!isset($subJob['listBlok'])) {
	                    # code...
	                    unset($subJob2[$key_sj]);
	                }
	            }
	        }

	        # MASUKIN PLOT-BARIS-POKOK
	        foreach ($subJob2 as $key_sj => $subJob) { #Plot
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    # code...
	                    foreach ($listPlot2 as $key_lp => $listPlot) {
	                        # code...
	                        if ($listPlot['codeBlok'] == $listBlok['blok']) {
	                            # code...
	                            unset($listPlot['codeBlok']);
	                            $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][] = $listPlot;
	                        }
	                    }
	                }
	            }
	        }
	        foreach ($subJob2 as $key_sj => $subJob) {#Baris
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            foreach ($listBaris2 as $key_lb => $listBaris) {
	                                # code...
	                                if (($listBaris['codeBlok'] == $listBlok['blok']) &&
	                                    ($listBaris['plot'] == $listPlot['plot'])) {
	                                    # code...
	                                    if ($listBaris['baris'] >= $listBlok['rowStart']  && $listBaris['baris'] <= $listBlok['rowEnd']) {
	                                        # code...
	                                        unset($listBaris['codeBlok']);
	                                        unset($listBaris['plot']);
	                                        $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][] = $listBaris;
	                                    }

	                                    if (($listBlok['rowStart'] == 0) && ($listBlok['rowEnd'] == 0)) {
	                                        # code...
	                                        unset($listBaris['codeBlok']);
	                                        unset($listBaris['plot']);
	                                        $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][] = $listBaris;
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    # code...
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            if (isset($listPlot['listBaris'])) {
	                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
	                                    foreach ($listPokok2 as $key_lpk => $listPokok) {
	                                        # code...
	                                        if (
	                                            ($listBlok['blok'] == $listPokok['codeBlok']) 
	                                            && ($listPlot['plot'] == $listPokok['plot']) 
	                                            && ($listBaris['baris'] == $listPokok['baris'])
	                                            && ($listBlok['rkhCode'] == $listPokok['rkhCode'])
	                                            && ($subJob['subJobCode'] == $listPokok['subJobCode'])
	                                            && ($listBlok['mandorCode'] == $listPokok['codeMandor'])
	                                        ) {
	                                            # code...
	                                            unset($listPokok['Description']);
	                                            unset($listPokok['codeBlok']);
	                                            unset($listPokok['plot']);
	                                            unset($listPokok['baris']);
	                                            unset($listPokok['noTanam']);

	                                            $listPokok['code'] = $listPokok['codeTanaman'];
	                                            unset($listPokok['codeTanaman']);
	                                            $listPokok['week'] = $this->datediff('ww', $listPokok['PlantingDate'], now());
	                                            unset($listPokok['jmlMinggu']);
	                                            $date = date_create($listPokok['PlantingDate']);
	                                            $listPokok['date'] = date_format($date, 'd F Y');
	                                            unset($listPokok['PlantingDate']);

	                                            unset($listPokok['rkhCode']);
	                                            unset($listPokok['subJobCode']);
	                                            unset($listPokok['codeMandor']);
	                                            unset($listPokok['codeTukang']);

	                                            $tgl = date_create($listPokok['created_at']);
	                                            $listPokok['mandorTrans'] = date_format($tgl, 'd F Y H:i:s');
	                                            unset($listPokok['created_at']);

	                                            $listPokok = array_filter($listPokok);
	                                            $listPokok['status'] = 0;
	                                            // empty($listPokok['totalHand']) ? $listPokok['totalHand'] = ''  : $listPokok['totalHand'] = 'Terisi';
	                                            if (isset($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listTK'])) {
	                                                # code...
	                                                if (!in_array($listPokok['tk'], $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listTK'])) {
	                                                    # code...
	                                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listTK'][] = $listPokok['tk'];
	                                                }
	                                            }else{
	                                                $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listTK'][] = $listPokok['tk'];
	                                            }

	                                            if (isset($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listMandor'])) {
	                                                # code...
	                                                if (!in_array($listPokok['tk'], $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listTK'])) {
	                                                    # code...
	                                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listMandor'][] = $listPokok['mandor'];
	                                                }
	                                            }else{
	                                                $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listMandor'][] = $listPokok['mandor'];
	                                            }

	                                            unset($listPokok['tk']);
	                                            unset($listPokok['mandor']);
	                                            unset($listPokok['mandorTrans']);

	                                            // $listPokok['id'] = $listBlok['rkhCode'].';'.$subJob['subJobCode'].';'.$listPokok['id'];
	                                            unset($listPokok['id']);
	                                            
	                                            unset($listPokok['date']);
	                                            $week = $listPokok['week'];
	                                            unset($listPokok['week']);
	                                            $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][] = $listPokok;

	                                            $listPokok['week'] = $week;
	                                            unset($listPokok['status']);
	                                            unset($listPokok['mandor']);
	                                            unset($listPokok['tk']);
	                                            unset($listPokok['mandorTrans']);
	                                            $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listAllPokokPlot'][] = $listPokok;
	                                        }
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        unset($week);
	        # MASUKIN BLOK-PLOT-BARIS-TANAM

	        # FILTERING TANAM
	        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    # code...
	                    // unset($subJob2[$key_sj]['listBlok'][$key_lt]['rkhDate']);
	                    unset($subJob2[$key_sj]['listBlok'][$key_lt]['mandorCode']);
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            if (isset($listPlot['listBaris'])) {
	                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
	                                    if (!isset($listBaris['listPokok'])) {
	                                        # code...
	                                        unset($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]);
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    # code...
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            if (isset($listPlot['listBaris'])) {
	                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
	                                    # code...
	                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'] = array_values($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris']);
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        foreach ($subJob2 as $key_sj => $subJob) {#Plot-Baris
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    # code...
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            if (!isset($listPlot['listBaris']) || empty($listPlot['listBaris'])) {
	                                unset($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]);
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        foreach ($subJob2 as $key_sj => $subJob) {#Plot-Baris
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    # code...
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'] = array_values($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot']);
	                        }
	                    }
	                }
	            }
	        }
	        # FILTERING TANAM

	        # MASUKIN STATUS KE POKOK
	        # tanaman sudah dikerjakan
	        $trans_kawil2 = $this->removeWhitespace(DB::table('EWS_TRANS_KAWIL')
	            ->select('id', 'idEWSTransMandor')
	            ->where('userid', '=', $user2[0]['id'])
	            // ->whereBetween('created_at', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
	            ->get());
	        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                        foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
	                            foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
	                                # code...
	                                // return $listPokok;
	                                foreach ($trans_kawil2 as $key_tm => $trans_kawil) {
	                                    # code...
	                                    if ($trans_kawil['idEWSTransMandor'] == $listPokok['idTransMandor']) {
	                                        # code...
	                                        // return 'ada';
	                                        $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][$key_lpk]['status'] = 1;
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	        # MASUKIN STATUS KE POKOK

	        # MENGHITUNG TOTAL STATUS 0 || 1
	        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
	            # code...
	            if (isset($subJob['listBlok'])) {
	                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
	                    $lpDone = 0;
	                    $lpNDone = 0;
	                    if (isset($listBlok['listPlot'])) {
	                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
	                            $lbDone = 0;
	                            $lbNDone = 0;
	                            if (isset($listPlot['listBaris'])) {
	                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
	                                    $lpkDone = 0;
	                                    $lpkNDone = 0;
	                                    if (isset($listBaris['listPokok'])) {
	                                        foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
	                                            # code...
	                                            if ($listPokok['status'] == 1) {
	                                                # code...
	                                                $lpkDone++;
	                                                unset($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][$key_lpk]['idTransMandor']);
	                                            }
	                                            else{
	                                                $lpkNDone++;
	                                                unset($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][$key_lpk]['idTransMandor']);
	                                            }
	                                        }
	                                    }
	                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['pokokDone'] = $lpkDone;
	                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['pokokNDone'] = $lpkNDone;
	                                    $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb], 'pokokNDone');
	                                    $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb], 'pokokDone');
	                                    $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb], 'baris');

	                                    $lbDone+=$lpkDone;
	                                    $lbNDone+=$lpkNDone;
	                                }
	                                $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['rowDone'] = $lbDone;
	                                $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['rowNDone'] = $lbNDone;
	                                $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp], 'rowNDone');
	                                $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp], 'rowDone');
	                                $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp], 'plot');

	                                $lpDone+=$lbDone;
	                                $lpNDone+=$lbNDone;
	                            }
	                        }
	                        $subJob2[$key_sj]['listBlok'][$key_lt]['plotDone'] = $lpDone;
	                        $subJob2[$key_sj]['listBlok'][$key_lt]['plotNDone'] = $lpNDone;
	                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'plotNDone');
	                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'plotDone');
	                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'blok');
	                    }
	                }
	            }
	        }
	        # MENGHITUNG TOTAL STATUS 0 || 1
	        foreach ($job2 as $key_j => $job) {
	            # code...
	            foreach ($subJob2 as $key_sj => $subJob) {
	                # code...
	                $parentJob = '';
	                switch ($job['Description']) {
	                    case 'PLANT CARE':
	                        $parentJob = 'plantCare';
	                        break;

	                    case 'FRUIT CARE':
	                        $parentJob = 'fruitCare';
	                        break;

	                    case 'PANEN':
	                        $parentJob = 'panen';
	                        break;
	                    case 'PACKING HOUSE':
	                        $parentJob = 'packingHouse';
	                        break;
	                }
	                $job2[$key_j][$parentJob]['jobCode'] = $job['jobCode'];
	                $jobdesc = ucwords(strtolower($job['Description']));
	                $job2[$key_j][$parentJob]['jenisPekerjaan'] = $jobdesc;
	                if ($subJob['jobCode'] == $job['jobCode']) {
	                    # code...
	                    unset($subJob['codeAlojob']);
	                    unset($subJob['rkhTime']);
	                    unset($subJob['mandorCode']);
	                    $job2[$key_j][$parentJob]['listChildJob'][] = $subJob;
	                }
	            }
	            unset($job2[3]); # menghapus packing house
	            unset($job2[$key_j]['jobCode']);
	            unset($job2[$key_j]['Description']);
	        }

	        $user2[0]['RKM'] = $job2;

	        ############################# CORRECTIVE ACTION #############################
	        $user2[0]['CA'] = array();
	        $listBlokCA = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_SENSUS')
	        	->select('codeBlok as blok')
	            ->distinct('codeBlok')
	        	->join('EWS_LOK_TANAMAN', 'EWS_TRANS_SPI_SENSUS.codeTanaman', '=', 'EWS_LOK_TANAMAN.codeTanaman')
	        	->where('useridKawil', NULL)
	        	->get());
	        $listPlotCA = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_SENSUS')
	        	->select('codeBlok as blok', 'plot')
	            ->distinct('codeBlok')
	        	->join('EWS_LOK_TANAMAN', 'EWS_TRANS_SPI_SENSUS.codeTanaman', '=', 'EWS_LOK_TANAMAN.codeTanaman')
	        	->where('useridKawil', NULL)
	        	->get());
	        $listBarisCA = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_SENSUS')
	        	->select('codeBlok as blok', 'plot', 'baris')
	            ->distinct('codeBlok')
	        	->join('EWS_LOK_TANAMAN', 'EWS_TRANS_SPI_SENSUS.codeTanaman', '=', 'EWS_LOK_TANAMAN.codeTanaman')
	        	->where('useridKawil', NULL)
	        	->get());
	        $listPokokCA = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_SENSUS')
	        	->select('EWS_TRANS_SPI_SENSUS.codeTanaman as code', 'EWS_LOK_TANAMAN.codeBlok as blok', 'EWS_LOK_TANAMAN.plot', 'EWS_LOK_TANAMAN.baris')
	        	->selectRaw('MAX(EWS_TRANS_SPI_SENSUS.week) as week')
	        	->join('EWS_LOK_TANAMAN', 'EWS_TRANS_SPI_SENSUS.codeTanaman', '=', 'EWS_LOK_TANAMAN.codeTanaman')
	        	->where('useridKawil', NULL)
	        	->groupBy('EWS_TRANS_SPI_SENSUS.codeTanaman', 'EWS_LOK_TANAMAN.codeBlok', 'EWS_LOK_TANAMAN.plot', 'EWS_LOK_TANAMAN.baris')
	        	->get());

	        # MASUKIN PLOT-BARIS-POKOK ke dalam BLOK
	        for ($a=0; $a < count($listBlokCA); $a++) { 
	        	$dataBlok[$a] = $listBlokCA[$a];
	        	for ($b=0; $b < count($listPlotCA); $b++) {
	        		if ($dataBlok[$a]['blok'] == $listPlotCA[$b]['blok']) {
	        		 	$dataBlok[$a]['listPlot'][$b] = $listPlotCA[$b];
		        		for ($c=0; $c < count($listBarisCA); $c++) { 
		        			if 	(($dataBlok[$a]['listPlot'][$b]['plot'] == $listBarisCA[$c]['plot']) && 
		        				($dataBlok[$a]['listPlot'][$b]['blok'] == $listBarisCA[$c]['blok'])) {
			        		 	$dataBlok[$a]['listPlot'][$b]['listBaris'][$c] = $listBarisCA[$c];
			        			for ($d=0; $d < count($listPokokCA); $d++) { 
			        				$pokok = $listPokokCA[$d];
				        			if	(($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['blok'] == $listPokokCA[$d]['blok']) &&
			        				 	($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['plot'] == $listPokokCA[$d]['plot']) && 
				        				($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['baris'] == $listPokokCA[$d]['baris'])) {

					        		 	unset($pokok['blok']);
		                                unset($pokok['plot']);
		                                unset($pokok['baris']);
		                                $week = $pokok['week'];
										unset($pokok['week']);

					        		 	$dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'][$d] = $pokok;

					        		 	$pokok['week'] = $week;
				        				$allPokokPlot = $pokok;
				      //   				unset($allPokokPlot['blok']);
										// unset($allPokokPlot['plot']);
										// unset($allPokokPlot['baris']);
										// unset($allPokokPlot['noTanam']);
										// unset($allPokokPlot['status']);
										// unset($allPokokPlot['date']);
				        		 		$dataBlok[$a]['listPlot'][$b]['listAllPokokPlot'][] = $allPokokPlot;
				        			}
			        			}
		        				unset($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['blok']);
                                unset($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['plot']);
		        				$dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok']);
		        			}
		        		}
		        		unset($dataBlok[$a]['listPlot'][$b]['blok']);
		        		$dataBlok[$a]['listPlot'][$b]['listBaris'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris']);
        		 	} 
	        	}
        		$dataBlok[$a]['listPlot'] = array_values($dataBlok[$a]['listPlot']);
	        }

	        $user2[0]['CA'] = $dataBlok;

	        return $user2;
	    }
	    
	    public function storeMandor (Request $request)
	    {
	        $validator = Validator::make($request->all(), [
	            'codeRKH' => 'required|between:0,25',
	            'subJobCode' => 'required|between:0,15',
	            'userid' => 'required',
	            'codeTukang' => 'required|between:0,20',
	            'codeTanaman' => 'required|between:0,20',
	            'note' => 'nullable|between:0,255',
	            'totalHand' => 'nullable|integer',
	            'totalFinger' => 'nullable|integer',
	            'totalLeaf' => 'nullable|integer',
	            'ribbonColor' => 'nullable|between:0,10',
	            'skimmingSize' => 'nullable',
	            'tanggal' => 'required',
	            'waktu' => 'required',
	            'pokokAwal' => 'nullable|integer',
	            'pokokAkhir' => 'nullable|integer'
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        if (isset($request->skimmingSize)) {
	        	# code...
	        	$request->skimmingSize = str_replace(',', '.', $request->skimmingSize);
	        }

	        $date = date_create($request->tanggal.' '.$request->waktu);
	        # tanggal bulan tahun
	        $created_at = date_format($date, 'Y-m-d H:i:s.B');

	        if ((isset($request->pokokAwal) && !empty($request->pokokAwal)) && 
	            ((isset($request->pokokAkhir) && !empty($request->pokokAkhir)))) {
	            # 1. PLANTCARE, MODEL PEMUPUKAN
	            # code...
	            $aw = $request->pokokAwal;
	            $ak = $request->pokokAkhir;

	            $message = array(
	                'code' => 200,
	                'message' => []
	            );

	            while ($aw <= $ak) {
	                # code...
	                $aw = str_pad($aw, 3, "0", STR_PAD_LEFT); # nambahin angka 0 di setiap digit satuan
	                $codeTanam = substr($request->codeTanaman, 0, strrpos($request->codeTanaman, '.')).'.'.$aw; #replace code pokok akhir dengan pokok skrg

	                # check data if exist
					$array = array(
	                        'rkhCode' => $request->codeRKH,
	                        'subJobCode' => $request->subJobCode,
	                        'userid' => $request->userid,
	                        'codeTanaman' => $codeTanam
	                    );
					$check = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
						->select('*')
						->where($array)
	                    ->get());
			        if (!empty($check)) 
			        {
	                    # code...
	                    // $message['message'][] = 'Data duplikat||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$codeTanam;
			        }
			        else
			        {
	                    $codeBlok = $this->removeWhitespace2(DB::table('EWS_LOK_TANAMAN')
	                        ->select('codeBlok')
	                        ->where('codeTanaman', '=', $codeTanam)
	                        ->first());
	                    if (empty($codeBlok)) {
	                        # code...
	                        // $message['message'][] = 'Data pokok tidak ada ||'.$codeTanam;
	                    }
	                    else{
	    		        	DB::table('EWS_TRANS_MANDOR')->insert([
	    	                    'rkhCode' => $request->codeRKH,
	    	                    'subJobCode' => $request->subJobCode,
	    	                    'userid' => $request->userid,
	    	                    'codeTukang' => $request->codeTukang,
	                            'codeBlok' => $codeBlok['codeBlok'],
	    	                    'codeTanaman' => $codeTanam,
	    	                    'mandorNote' => $request->note,
	    	                    'totalHand' => $request->totalHand,
	    	                    'totalFinger' => $request->totalFinger,
	    	                    'totalLeaf' => $request->totalLeaf,
	    	                    'ribbonColor' => $request->ribbonColor,
	    	                    'skimmingSize' => $request->skimmingSize,
	    	                    'created_at' => $created_at
	                        ]);
	                        $message['message'][] = 'Data berhasil di input||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$codeTanam;
	                    }
			        }
	                $aw++;
	            }
	            return response()->json($message, 200);
	        }
	        else
	        {
	            # 2. FRUIT CARE, MODEL BI [NORMAL]
	            # check data if exist
	            $message = array(
	                'code' => 200,
	                'message' => []
	            );
				$array = array(
		                    'rkhCode' => $request->codeRKH,
		                    'subJobCode' => $request->subJobCode,
		                    'userid' => $request->userid,
		                    'codeTanaman' => $request->codeTanaman
		                );
				$check = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
					->select('*')
					->where($array)
		            ->get());
		        if (!empty($check)) 
		        {
		        	# code...
	                // $message['message'][] = 'Data duplikat||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$request->codeTanaman;
		        }
		        else
		        {
	                $codeBlok = $this->removeWhitespace2(DB::table('EWS_LOK_TANAMAN')
	                    ->select('codeBlok')
	                    ->where('codeTanaman', '=', $request->codeTanaman)
	                    ->first());
	                if (empty($codeBlok)) {
	                    # code...
	                    // $message['message'][] = 'Data pokok tidak ada ||'.$request->codeTanaman;
	                }
	                else{
	    	            DB::table('EWS_TRANS_MANDOR')->insert([
	    	                'rkhCode' => $request->codeRKH,
	    	                'subJobCode' => $request->subJobCode,
	    	                'userid' => $request->userid,
	    	                'codeTukang' => $request->codeTukang,
	                        'codeBlok' => $codeBlok['codeBlok'],
	    	                'codeTanaman' => $request->codeTanaman,
	    	                'mandorNote' => $request->note,
	    	                'totalHand' => $request->totalHand,
	    	                'totalFinger' => $request->totalFinger,
	    	                'totalLeaf' => $request->totalLeaf,
	    	                'ribbonColor' => $request->ribbonColor,
	    	                'skimmingSize' => $request->skimmingSize,
	    	                'created_at' => $created_at
	    	            ]);
	                    $message['message'][] = 'Data berhasil di input||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$request->codeTanaman;
	                }
	            }
	            return response()->json($message, 200);
	        }
	    }

	    public function storeKawil (Request $request)
	    {
	    	if (isset($request->corrective) && $request->corrective == 1) {
	        	$validator = Validator::make($request->all(), [
		            'codeTanaman' => 'required',
		            'week' => 'required',
		            'note' => 'nullable|between:0,255',
		            'userid' => 'required|integer',
		            'tanggal' => 'required',
		            'waktu' => 'required'
		        ]);

		        if ($validator->fails()) {
		            return $this->errMessage(400,$validator->messages()->first());
		        }

		        $date = date_create($request->tanggal.' '.$request->waktu);
		        # tanggal bulan tahun
		        $tbt = date_format($date, 'Y-m-d H:i:s.B');

		        $data = array(
		                'corrActKawil' => $request->note,
		                'created_atKawil' => $tbt,
		                'useridKawil' => $request->userid,
		            );

		    	$check = DB::table('EWS_TRANS_SPI_SENSUS')
		    		->where('codeTanaman', $request->codeTanaman)
		    		->where('week', $request->week)
		    		->where('useridKawil', NULL)
		    		->value('id');
		        if (empty($check)) {
		            # code...
		            try {
		                // DB::table('EWS_TRANS_PH_BT')->insert($data);
		                $message['message'][] = 'Data tidak ada';
		                // $message['message'][] = $data;
		            } catch (\Exception  $e) {
		                $message['message'][] = $e->getMessage();
		            }
		        }else{
		            try {
		                DB::table('EWS_TRANS_SPI_SENSUS')->where('id', $check)->update($data);
		                $message['message'][] = 'Data berhasil di update';
		                // $message['message'][] = $data;
		            } catch (\Exception  $e) {
		                $message['message'][] = $e->getMessage();
		            }
		        }

	            return response()->json($message, 200);
	        }
	        else {
		        # validasi request
		        $validator = Validator::make($request->all(), [
		            'codeRKH' => 'required|between:0,25',
		            'subJobCode' => 'required|between:0,15',
		            'userid' => 'required',
		            'codeTanaman' => 'required|between:0,20',
		            'note' => 'nullable|between:0,255',
		            'tanggal' => 'required',
		            'waktu' => 'required',
		            'pokokAwal' => 'nullable|integer',
		            'pokokAkhir' => 'nullable|integer',
		        ]);
		        # return err ketika validasi tidak sesuai
		        if ($validator->fails()) {
		            return $this->errMessage(400,$validator->messages()->first());
		        }



		        # pembuatan tanggal
		        $date = date_create($request->tanggal.' '.$request->waktu);
		        # tanggal bulan tahun
		        $created_at = date_format($date, 'Y-m-d H:i:s.B');
		        # bikin template pesan
		        $message = array(
		            'code' => 200,
		            'message' => []
		        );

		        # kalau ada pokokAwal dan pokokAkhir
		        if ((isset($request->pokokAwal) && !empty($request->pokokAwal)) && 
		            ((isset($request->pokokAkhir) && !empty($request->pokokAkhir)))) {
		            # 1. PLANTCARE, MODEL PEMUPUKAN
		            # code...
		            $aw = $request->pokokAwal;
		            $ak = $request->pokokAkhir;

		            # kalau pokokAwal lebih kecil dr pokokAkhir
		            while ($aw <= $ak) {
		                $aw = str_pad($aw, 3, "0", STR_PAD_LEFT); # nambahin angka 0 di setiap digit satuan
		                $codeTanam = substr($request->codeTanaman, 0, strrpos($request->codeTanaman, '.')).'.'.$aw; #replace code pokok akhir dengan pokok skrg

		                # ambil id trans mandor di tabel ews_trans_mandor brdsrkn rkhcode, subjobcode, codetanaman
		                $array = array(
		                        'rkhCode' => $request->codeRKH,
		                        'subJobCode' => $request->subJobCode,
		                        'codeTanaman' => $codeTanam
		                    );
		                $check = DB::table('EWS_TRANS_MANDOR')
		                    ->where($array)
		                    ->value('id');
		                # cek id trans mandor
		                if (empty($check)) 
		                {
		                    # jika id trans mandor tidak ada
		                    $message['message'][] = 'Data tidak ditemukan||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$codeTanam;
		                }
		                else
		                {
		                    # ada id trans mandor
		                    # check id trans mandor jika ada duplikat di ews_trans_kawil
		                    $arrCheck = array('check' => $check);
		                    $validator = Validator::make($arrCheck, [
		                        'check' => 'unique:EWS_TRANS_KAWIL,idEWSTransMandor'
		                    ]);
		                    if ($validator->fails()) {
		                        # id trans mandor duplikat
		                        $message['message'][] = $validator->messages()->first().'||'.$check;
		                    }else{
		                        # id trans mandor belum di insert
		                        # insert ke ews_trans_kawil
		                        DB::table('EWS_TRANS_KAWIL')->insert([
		                            'idEWSTransMandor' => $check,
		                            'kawilNote' => $request->note,
		                            'userid' => $request->userid,
		                            'created_at' => $created_at
		                        ]);
		                        $message['message'][] = 'Data berhasil di input||'.$check.'||'.$request->note.'||'.$request->userid.'||'.$created_at;
		                    }
		                }
		                # ke pokok selanjutnya
		                $aw++;
		            }
		            # return pesan semua
		            return response()->json($message, 200);
		        }
		        # kalau tidak ada pokokAwal pokokAkhir
		        else
		        {
		            # ambil id trans mandor di tabel ews_trans_mandor brdsrkn rkhcode, subjobcode, codetanaman
		            $array = array(
		                    'rkhCode' => $request->codeRKH,
		                    'subJobCode' => $request->subJobCode,
		                    'codeTanaman' => $request->codeTanaman
		                );
		            $idEwsTransMandor = DB::table('EWS_TRANS_MANDOR')
		                ->where($array)
		                ->value('id');
		            # cek id trans mandor
		            if (empty($idEwsTransMandor)) 
		            {
		                # jika id trans mandor tidak ada
		                // $message['message'][] = 'Data tidak ditemukan||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->codeTanaman;
		            }
		            else
		            {
		                # ada id trans mandor
		                # cek id trans mandor jika ada duplikat di ews_trans_kawil
		                $arrCheck = array('idEwsTransMandor' => $idEwsTransMandor);
		                $validator = Validator::make($arrCheck, [
		                    'idEwsTransMandor' => 'unique:EWS_TRANS_KAWIL,idEWSTransMandor'
		                ]);
		                if ($validator->fails()) {
		                    # id trans mandor duplikat
		                    $message['message'][] = $validator->messages()->first().'||'.$idEwsTransMandor;
		                }else{
		                    # id trans mandor belum di insert
		                    # insert ke ews_trans_kawil
		                    DB::table('EWS_TRANS_KAWIL')->insert([
		                        'idEWSTransMandor' => $idEwsTransMandor,
		                        'kawilNote' => $request->note,
		                        'userid' => $request->userid,
		                        'created_at' => $created_at
		                    ]);
		                    $message['message'][] = 'Data berhasil di input||'.$idEwsTransMandor.'||'.$request->note.'||'.$request->userid.'||'.$created_at;
		                }
		            }
		            return response()->json($message, 200);
		        }
	        }
	    }

	    public function getAllPokok()
	    {
	        # code...
	        $pokok2      = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	        	->orderBy('codeTanaman', 'asc')
	        	->get());
	        foreach ($pokok2 as $key_pk => $pokok) {
	            # code...
	            $pokok2[$key_pk]['jmlMinggu'] = $this->datediff('ww', $pokok['PlantingDate'], now());
	        }
	        return $pokok2;
	    }

	    public function getCTPokok()
	    {
	        # code...
	        $pokok2      = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	        	->select('codeTanaman')
	        	->orderBy('codeTanaman', 'asc')
	        	->get());
	        return $pokok2;
	    }

	    public function getTreePokok()
	    {
	        $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok')
	            ->distinct('codeBlok')
	            ->orderBy('codeBlok', 'asc')
	            ->get());
	        $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok', 'plot')
	            ->distinct('codeBlok')
	            ->orderBy('plot', 'asc')
	            ->get());
	        $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok','plot', 'baris')
	            ->distinct('baris')
	            ->orderBy('baris', 'asc')
	            ->get());
	        $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->orderBy('codeTanaman', 'asc')
	        	->get());

	        # collect all data
	        $tree2 = $listBlok2;

	    	foreach ($tree2 as $key_t => $tree) {
	        	# code...
	        	foreach ($listPlot2 as $key_lp => $listPlot) {
	        		# code...
	        		if ($listPlot['codeBlok'] == $tree['codeBlok']) {
	        			# code...
	        			unset($listPlot['codeBlok']);
	        			$tree2[$key_t]['listPlot'][] = $listPlot;
	        		}
	        	}
	        }

	    	foreach ($tree2 as $key_t => $tree) {
	        	# code...
	        	foreach ($tree['listPlot'] as $key_lp => $listPlot) {
	        		# code...
	        		foreach ($listBaris2 as $key_lb => $listBaris) {
	        			# code...
	        			if (($listBaris['codeBlok'] == $tree['codeBlok']) &&
	                    	($listBaris['plot'] == $listPlot['plot'])) {
	        				# code...
	        				unset($listBaris['codeBlok']);
	        				unset($listBaris['plot']);
	        				$tree2[$key_t]['listPlot'][$key_lp]['listBaris'][] = $listBaris;
	        			}
	        		}
	        	}
	        }

			foreach ($tree2 as $key_t => $tree) {
	        	# code...
	        	foreach ($tree['listPlot'] as $key_lp => $listPlot) {
	        		# code...
	        		foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
	        			# code...
	        			foreach ($listPokok2 as $key_pk => $listPokok) {
	        				# code...
	        				if (($listPokok['codeBlok'] == $tree['codeBlok']) && 
	                         ($listPokok['plot'] == $listPlot['plot']) && 
	                         ($listPokok['baris'] == $listBaris['baris'])) {
	                            # code...
	                            unset($listPokok['id']);
	                        	unset($listPokok['Description']);
	                        	unset($listPokok['codeBlok']);
	                        	unset($listPokok['plot']);
	                        	unset($listPokok['baris']);
	                        	unset($listPokok['noTanam']);
	                        	unset($listPokok['PlantingDate']);
	                            $tree2[$key_t]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][] = $listPokok;
	                        }
	        			}
	        		}
	        	}
	        }

	        return $tree2;
	    }
    # /MANDOR KAWIL PLANTCARE-FRUITCARE-PANEN #

    # PACKING HOUSE BERAT TANDAN-HITUNG TANDAN-CEK LIST TIMBANG #
	    public function getPH($user2, $identitasPekerja, $detailRole, $loginAs)
	    {
	        # membuat identitas user
	        unset($user2[0]['codePekerja']);
	        $user2[0]['data'] = $loginAs;
	        unset($identitasPekerja['idRole']);
	        $user2[0]['identitasPekerja'] = $identitasPekerja;
	        $user2[0]['identitasPekerja']['detailRole'] = $detailRole;

	        # memasukkan TK PH
	        $TK = $this->removeWhitespace(DB::table('EWS_PH_TK')
	            ->select('namaPekerja as nama', 'codePekerja as code')
	            ->orderBy('namaPekerja', 'asc')
	            ->get());
	        $pilihTukang = array(
	            'id' => '',
	            'nama' => 'Pilih Pekerja',
	            'code' => ''
	        );
	        array_unshift($TK, $pilihTukang); #inserts new elements into beginning of array
	        $user2[0]['identitasPekerja']['detailPekerja']['tukang'] = $TK;

	        $date_frst = date('Y-m-d');
	        $date_scnd = date('Y-m-d', strtotime('-7 days'));

	        # mencari blok-blok dari rkh panen selama seminggu terakhir
	        $listBlokPanen  = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
	            ->select('codeBlok as blok')
	            ->distinct('codeBlok')
	            ->orderBy('codeBlok', 'asc')
	            ->where('codeAlojob', '=', '5410400000')
	            ->whereBetween('rkhDate', [$date_scnd.' 00:00:00.000', $date_frst.' 23:59:59.000'])
	            ->get());
	        if (empty($listBlokPanen)) {
	            # code...
	            return $this->errMessage(400,'Tidak ada pokok yang di panen dari '.$date_frst.' sampai '.$date_scnd);
	        }
	        foreach ($listBlokPanen as $key => $value) {
	        	# code...
	        	$listBlok[] = str_replace('.', '-', $value['blok']);
	            $listBlok = array_unique($listBlok);
        		$listBlok = array_values($listBlok);
	        }
	        
	        # mencari tanaman panen selama seminggu terakhir dari rkh
	        $listPokokPanen = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            // ->select('id', 'subJobCode', 'codeTanaman', 'rkhCode', 'codeBlok')
	            ->select('id', 'codeTanaman as code', 'codeBlok as blok')
	            ->whereIn('codeBlok', $listBlok)
	            ->orderBy('codeTanaman', 'asc')
	            ->get());
	        if (empty($listPokokPanen)) {
	            # code...
	            return $this->errMessage(400,'Tidak ada blok tanaman yang sesuai dengan rkh panen');
	        }

	        # ambil semua transaksi mandor dari aktifitas marking untuk diambil ribbonColor-nya
	        $listMarking = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
	            ->select('id', 'codeTanaman as code', 'ribbonColor')
	            ->where('subJobCode', '=', '5210410800') # marking
	            ->get());
	        
	        # ambil kategori pekerjaan
	        $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());

	        if ($user2[0]['data'] == 'tandan' || $user2[0]['data'] == 'bonggol') {
		        # membuat data untuk BT (Bruto & Bonggol)
		        $listBT = $listBlokPanen;
		        foreach ($listBT as $lbp => $blokPanen) {
		            foreach ($listPokokPanen as $lpp => $pokokPanen) {
		                # masukkan pokok sesuai dengan bloknya
		                if ($blokPanen['blok'] == $pokokPanen['blok']) {
		                    # pokok panen id dibuat dari id pada aktifitas panen di tabel trans mandor.
		                    if ($user2[0]['data'] == 'tandan') {
		                    	# code...
		                    	$pokokPanen['id'] = 'BT;'.$pokokPanen['id'];
		                    }
		                    if ($user2[0]['data'] == 'bonggol') {
		                    	# code...
		                    	$pokokPanen['id'] = 'BB;'.$pokokPanen['id'];
		                    }
		                    $pokokPanen['status'] = 0;
		                    // unset($pokokPanen['rkhCode']);
		                    // unset($pokokPanen['subJobCode']);
		                    unset($pokokPanen['blok']);
		                    $listBT[$lbp]['listPokok'][] = $pokokPanen; 
		                }
		            }
		        }
		        # masukin status 1 bagi yang sudah di input
		        $trans_bt = $this->removeWhitespace(DB::table('EWS_TRANS_PH_BT')->get());
		        foreach ($listBT as $lbp => $blokPanen) {
		            foreach ($blokPanen['listPokok'] as $lpp => $pokokPanen) {
		                foreach ($trans_bt as $tbt => $trans) {
		                    // $pokok = explode(';',$pokokPanen['id']);
		                    // if ($pokok[1] == $trans['idEWSTransMandor']) {
		                    if ($pokokPanen['code'] == $trans['codeTanaman']) {
		                        # count for bruto
		                        if (($user2[0]['data'] == 'tandan') && (!empty($trans['brutoUserid'])))  {
		                            $listBT[$lbp]['listPokok'][$lpp]['status'] = 1; 
		                        }

		                        # count for bruto
		                        if (($user2[0]['data'] == 'bonggol') && (!empty($trans['bonggolUserid']))) {
		                            $listBT[$lbp]['listPokok'][$lpp]['status'] = 1; 
		                        }
		                    } 
		                }
		            }
		        }
		        # menghitung jumlah status 0 dan 1
		        foreach ($listBT as $lbp => $blokPanen) {
		            $pokokDone = 0;
		            $pokokNDone = 0;
		            foreach ($blokPanen['listPokok'] as $lpp => $pokokPanen) {
		                if ($pokokPanen['status'] == 1) {
		                    $pokokDone++;
		                }else{
		                    $pokokNDone++;
		                }
		            }
		            $listBT[$lbp]['pokokDone'] = $pokokDone;
		            $listBT[$lbp]['pokokNDone'] = $pokokNDone;

		            $this->move_to_top($listBT[$lbp], 'pokokNDone');
		            $this->move_to_top($listBT[$lbp], 'pokokDone');
		            $this->move_to_top($listBT[$lbp], 'blok');
		        	unset($listBT[$lbp]['listPokok']);
		        }
		        if ($user2[0]['data'] == 'tandan') {
		        	# code...
			        $listChildJob = array(array(
		                'subJobCode' => 1,
		                'Description' => 'Berat Tandan',
		                'listBlok' => $listBT
		            ));
		        }else {
		            $listChildJob = array(array(
		                'subJobCode' => 2,
		                'Description' => 'Berat Bonggol',
		                'listBlok' => $listBT
		            ));
		        }
	        }

	        if ($user2[0]['data'] == 'qc') {
		        # membuat data untuk HT (Bruto & Bonggol)
		        $listHT = $listBlokPanen;
		        foreach ($listHT as $lbp => $blokPanen) {
		        	$blokTtk = str_replace('-', '.', $blokPanen['blok']);
		            foreach ($listPokokPanen as $lpp => $pokokPanen) {
		                # masukkan pokok sesuai dengan bloknya
		                if ($blokPanen['blok'] == $pokokPanen['blok']) {
		                	// $pokokPanen['code'] = str_replace($blokTtk.'.', '', $pokokPanen['code']);
		                    # pokok panen id dibuat dari id pada aktifitas panen di tabel trans mandor.
		                    $pokokPanen['stat'] = 0;
		                    unset($pokokPanen['id']);
		                    unset($pokokPanen['blok']);
                            $pokokPanen['ribClr'] = '-';
		                    $listHT[$lbp]['listPokok'][] = $pokokPanen;
		                }
		            }
		        }
		        foreach ($listHT as $lbp => $blokPanen) {
		            foreach ($blokPanen['listPokok'] as $lp => $pokok) {
		                foreach ($listMarking as $lm => $marking) {
		                    if ($pokok['code'] == $marking['code']) {
		                        $listHT[$lbp]['listPokok'][$lp]['ribClr'] = $marking['ribbonColor'];
		                    } 
		                }
		            }
		        }
		        # masukin status 1 bagi yang sudah di input
		        $trans_ht = $this->removeWhitespace(DB::table('EWS_TRANS_PH_HT')->get());
		        foreach ($listHT as $lbp => $blokPanen) {
		            foreach ($blokPanen['listPokok'] as $lpp => $pokokPanen) {
		                foreach ($trans_ht as $tht => $trans) {
		                    // $pokok = explode(';',$pokokPanen['id']);
		                    // if ($pokok[1] == $trans['idEWSTransMandor']) {
		                    if ($pokokPanen['code'] == $trans['codeTanaman']) {
		                        $listHT[$lbp]['listPokok'][$lpp]['stat'] = 1; 
		                    } 
		                }
		            }
		        }
		        # menghitung jumlah status 0 dan 1
		        foreach ($listHT as $lbp => $blokPanen) {
		            $pokokDone = 0;
		            $pokokNDone = 0;
		            foreach ($blokPanen['listPokok'] as $lpp => $pokokPanen) {
		                if ($pokokPanen['stat'] == 1) {
		                    $pokokDone++;
		                }else{
		                    $pokokNDone++;
		                }
		            }
		            $listHT[$lbp]['pokokDone'] = $pokokDone;
		            $listHT[$lbp]['pokokNDone'] = $pokokNDone;

		            $this->move_to_top($listHT[$lbp], 'pokokNDone');
		            $this->move_to_top($listHT[$lbp], 'pokokDone');
		            $this->move_to_top($listHT[$lbp], 'blok');
		        }

	            # menghapus status di dalam listPokok
		        foreach ($listHT as $lbp => $blokPanen) {
		            foreach ($blokPanen['listPokok'] as $lpp => $pokokPanen) {
		            	unset($listHT[$lbp]['listPokok'][$lpp]['stat']);
		            }
		        }
		        $listChildJob = array(array(
	                'subJobCode' => 3,
	                'Description' => 'Quality Control',
	                'listBlok' => $listHT
	            ));
	        }

	        if ($user2[0]['data'] == 'clt') {
		        # membuat data untuk CLT, (Bruto & Bonggol)
		        $listCLT = $listBlokPanen;

		        $listProduk = $this->removeWhitespace(DB::table('EWS_CLT_PRODUK')->get());
		        $pilihProduk = array(
		            'id' => '0',
		            'desc' => 'Pilih Produk',
		        );
		        array_unshift($listProduk, $pilihProduk);
		        $listChildJob = array(array(
	                'subJobCode' => 4,
	                'Description' => 'Cek List Timbang',
	                'listProdukCLT' => $listProduk,
	                'listBlok' => $listCLT
	            ));
	        }

	        foreach ($job2 as $key_j => $job) {
	            # code...
	            $parentJob = '';
	            switch ($job['Description']) {
	                case 'PLANT CARE':
	                    $parentJob = 'plantCare';
	                    break;

	                case 'FRUIT CARE':
	                    $parentJob = 'fruitCare';
	                    break;

	                case 'PANEN':
	                    $parentJob = 'panen';
	                    break;
	                case 'PACKING HOUSE':
	                    $parentJob = 'packingHouse';
	                    break;
	            }
	            $job2[$key_j][$parentJob]['jobCode'] = $job['jobCode'];
	            $jobdesc = ucwords(strtolower($job['Description']));
	            $job2[$key_j][$parentJob]['jenisPekerjaan'] = $jobdesc;
	            if ($job['jobCode'] == '004') {
	                # code...
	                $job2[$key_j][$parentJob]['listChildJob'] = $listChildJob;
	            }
	            else {
	                unset($job2[$key_j]);
	            }
	            unset($job2[$key_j]['jobCode']);
	            unset($job2[$key_j]['Description']);
	        }
	        $job2 = $job2[3];
	        $user2[0]['RKM'] = array($job2);
	        
	        return $user2;
	    }
	    
	    public function storePH(Request $request) # store packing house
	    {
	        $validator = Validator::make($request->all(), [
	            'input' => [
	                'required', 
	                Rule::in(['bt', 'bb', 'qc', 'clt'])
	            ],
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        if ($request->input == 'bt') {
	            return $this->storeBT($request);
	        }

	        else if ($request->input == 'bb') {
	            return $this->storeBB($request);
	        }

	        else if ($request->input == 'qc') {
	            return $this->storeQC($request);
	        }

	        else if ($request->input == 'clt') {
	            return $this->storeCLT($request);
	        }

	        else {
	            return $this->errMessage(400,'Tidak ada input yang sesuai');
	        }
	    }

	    public function storeBT(Request $request) # STORE PROSESS PACKING HOUSE - BERAT TANDAN #
	    {
	        $validator = Validator::make($request->all(), [
	            'userid' => 'required',
	            'codePokok' => 'required',
	            'codeTK' => 'required|between:0,20',
	            'berat' => 'required',
	            'note' => 'nullable|between:0,255',
	            'tanggal' => 'required',
	            'waktu' => 'required'
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        $request->berat = str_replace(',', '.', $request->berat);

	        $date = date_create($request->tanggal.' '.$request->waktu);
	        # tanggal bulan tahun
	        $brutoDate = date_format($date, 'Y-m-d H:i:s.B');

	        $data = array(
	                'codeTanaman' => $request->codePokok,
	                'brutoUserid' => $request->userid,
	                'brutoTK' => $request->codeTK,
	                'brutoBerat' => $request->berat,
	                'brutoNote' => $request->note,
	                'brutoDate' => $brutoDate,
	            );
	        
	        return $this->insertupdatePH($data);
	    }

	    public function storeBB(Request $request) # STORE PROSESS PACKING HOUSE - BERAT BONGGOL #
	    {
	        $validator = Validator::make($request->all(), [
	            'userid' => 'required',
	            'codePokok' => 'required',
	            'codeTK' => 'required|between:0,20',
	            'berat' => 'required',
	            'note' => 'nullable|between:0,255',
	            'tanggal' => 'required',
	            'waktu' => 'required'
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        $request->berat = str_replace(',', '.', $request->berat);

	        $date = date_create($request->tanggal.' '.$request->waktu);
	        # tanggal bulan tahun
	        $bonggolDate = date_format($date, 'Y-m-d H:i:s.B');

	        $data = array(
	                'codeTanaman' => $request->codePokok,
	                'bonggolUserid' => $request->userid,
	                'bonggolTK' => $request->codeTK,
	                'bonggolBerat' => $request->berat,
	                'bonggolNote' => $request->note,
	                'bonggolDate' => $bonggolDate,
	            );

	        return $this->insertupdatePH($data);
	    }

	    public function insertupdatePH($data)
	    {
	        $check = DB::table('EWS_TRANS_PH_BT')->where('codeTanaman', $data['codeTanaman'])->value('id');
	        if (empty($check)) {
	            # code...
	            try {
	                DB::table('EWS_TRANS_PH_BT')->insert($data);
	                $message['message'][] = 'Data berhasil di input';
	                // $message['message'][] = $data;
	            } catch (\Exception  $e) {
	                $message['message'][] = $e->getMessage();
	            }
	        }else{
	            try {
	                DB::table('EWS_TRANS_PH_BT')->where('id', $check)->update($data);
	                $message['message'][] = 'Data berhasil di update';
	                // $message['message'][] = $data;
	            } catch (\Exception  $e) {
	                $message['message'][] = $e->getMessage();
	            }
	        }

	        return response()->json($message, 200);
	    }

	    public function storeQC(Request $request) # STORE PROSESS PACKING HOUSE - HITUNG TANDAN #
	    {
	        $validator = Validator::make($request->all(), [
	            'userid' => 'required',
	            'codeTK' => 'required|between:0,20',
	            'codePokok' => 'required',
	            'handClass' => 'required', 
	            'calHandClass2' => 'required', 
	            'calHandClass4' => 'required', 
	            'calHandClass6' => 'required', 
	            'calHandClass8' => 'required', 
	            'calHandClass10' => 'required', 
	            'calHandClassAkhir' => 'required', 
	            'fingerLen2' => 'required', 
	            'fingerLen4' => 'required', 
	            'fingerLen6' => 'required', 
	            'fingerLen8' => 'required', 
	            'fingerLen10' => 'required', 
	            'fingerLenAkhir' => 'required', 
	            'fingerHand2' => 'required', 
	            'fingerHand4' => 'required', 
	            'fingerHand6' => 'required', 
	            'fingerHand8' => 'required', 
	            'fingerHand10' => 'required', 
	            'fingerHandAkhir' => 'required', 
	            'note' => 'nullable|between:0,255',
	            'tanggal' => 'required',
	            'waktu' => 'required',
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        $request->handClass = str_replace(',', '.', $request->handClass);
			$request->calHandClass2 = str_replace(',', '.', $request->calHandClass2);
			$request->calHandClass4 = str_replace(',', '.', $request->calHandClass4);
			$request->calHandClass6 = str_replace(',', '.', $request->calHandClass6);
			$request->calHandClass8 = str_replace(',', '.', $request->calHandClass8);
			$request->calHandClass10 = str_replace(',', '.', $request->calHandClass10);
			$request->calHandClassAkhir = str_replace(',', '.', $request->calHandClassAkhir);
			$request->fingerLen2 = str_replace(',', '.', $request->fingerLen2);
			$request->fingerLen4 = str_replace(',', '.', $request->fingerLen4);
			$request->fingerLen6 = str_replace(',', '.', $request->fingerLen6);
			$request->fingerLen8 = str_replace(',', '.', $request->fingerLen8);
			$request->fingerLen10 = str_replace(',', '.', $request->fingerLen10);
			$request->fingerLenAkhir = str_replace(',', '.', $request->fingerLenAkhir);
			$request->fingerHand2 = str_replace(',', '.', $request->fingerHand2);
			$request->fingerHand4 = str_replace(',', '.', $request->fingerHand4);
			$request->fingerHand6 = str_replace(',', '.', $request->fingerHand6);
			$request->fingerHand8 = str_replace(',', '.', $request->fingerHand8);
			$request->fingerHand10 = str_replace(',', '.', $request->fingerHand10);
			$request->fingerHandAkhir = str_replace(',', '.', $request->fingerHandAkhir);

	        $date = date_create($request->tanggal.' '.$request->waktu);
	        # tanggal bulan tahun
	        $dateHT = date_format($date, 'Y-m-d H:i:s.B');

	        $data = array(
                'codeTanaman' => $request->codePokok,
	            'userid' => $request->userid,
	            'TK' => $request->codeTK,
	            'HandClass' => $request->handClass, 
	            'CalHandClass2' => $request->calHandClass2, 
	            'CalHandClass4' => $request->calHandClass4, 
	            'CalHandClass6' => $request->calHandClass6, 
	            'CalHandClass8' => $request->calHandClass8, 
	            'CalHandClass10' => $request->calHandClass10, 
	            'CalHandClassAkhir' => $request->calHandClassAkhir, 
	            'FingerLen2' => $request->fingerLen2, 
	            'FingerLen4' => $request->fingerLen4, 
	            'FingerLen6' => $request->fingerLen6, 
	            'FingerLen8' => $request->fingerLen8, 
	            'FingerLen10' => $request->fingerLen10, 
	            'FingerLenAkhir' => $request->fingerLenAkhir, 
	            'FingerHand2' => $request->fingerHand2, 
	            'FingerHand4' => $request->fingerHand4, 
	            'FingerHand6' => $request->fingerHand6, 
	            'FingerHand8' => $request->fingerHand8, 
	            'FingerHand10' => $request->fingerHand10, 
	            'FingerHandAkhir' => $request->fingerHandAkhir, 
	            'Notes' => $request->note,
	            'date' => $dateHT
	        );

	        $check = DB::table('EWS_TRANS_PH_HT')->where('codeTanaman', $data['codeTanaman'])->value('id');
	        if (empty($check)) {
	            # code...
	            try {
	                DB::table('EWS_TRANS_PH_HT')->insert($data);
	                $message['message'][] = 'Data berhasil di input';
	                // $message['message'][] = $data;
	            } catch (\Exception  $e) {
	                $message['message'][] = $e->getMessage();
	            }
	        }else{
	            try {
	                DB::table('EWS_TRANS_PH_HT')->where('id', $check)->update($data);
	                $message['message'][] = 'Data berhasil di update';
	                // $message['message'][] = $data;
	            } catch (\Exception  $e) {
	                $message['message'][] = $e->getMessage();
	            }
	        }

	        return response()->json($message, 200);
	    }

	    public function storeCLT(Request $request) # STORE PROSESS PACKING HOUSE - CEK LIST TIMBANG #
	    {
	        $listIdProd = json_decode(DB::table('EWS_CLT_PRODUK')->pluck('id'),TRUE); # convert object to array
	        $validator = Validator::make($request->all(), [
	            'userid' => 'required',
	            'codeBlok' => 'required',
	            'idProduk' => [
	                'required', 
	                Rule::in($listIdProd)
	            ],
	            'berat' => 'required',
	            'note' => 'nullable|between:0,255',
	            'tanggal' => 'required',
	            'waktu' => 'required'
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        $request->berat = str_replace(',', '.', $request->berat);

	        $date = date_create($request->tanggal.' '.$request->waktu);
	        # tanggal bulan tahun
	        $dateCLT = date_format($date, 'Y-m-d H:i:s.B');

	        $data = array(
	            'codeBlok' => $request->codeBlok,
	            'userid' => $request->userid,
	            'idProduk' => $request->idProduk,
	            'berat' => $request->berat, 
	            'note' => $request->note, 
	            'date' => $dateCLT
	        );

	        try {
	            DB::table('EWS_TRANS_PH_CLT')->insert($data);
	            $message['message'][] = 'Data berhasil di input';
	            // $message['message'][] = $data;
	        } catch (\Exception  $e) {
	            $message['message'][] = $e->getMessage();
	        }

	        return response()->json($message, 200);
	    }
    # /PACKING HOUSE BERAT TANDAN-HITUNG TANDAN-CEK LIST TIMBANG #

	# SPI
	    public function getSPI ($user2, $identitasPekerja, $detailRole, $request)
	    {
	    	$validator = Validator::make($request->all(), [
                'sensus' => 'required|boolean'
            ]);
            if ($validator->fails()) {
                return $this->errMessage(400,$validator->messages()->first());
            }

            if ($request->sensus == FALSE) {
            	$validator = Validator::make($request->all(), [
	                'mandor' => 'required|string',
	                'date' => 'required|date|date_format:d-m-Y'
	            ]);
	            if ($validator->fails()) {
	                return $this->errMessage(400,$validator->messages()->first());
	            }
		        $tgl = date_create($request->date);

		        $bln_tukang = date_format($tgl, 'm');
	        	$thn_tukang = date_format($tgl, 'Y');

	            $tukang = $this->removeWhitespace(DB::table('EWS_PEKERJA')
		            ->join('EWS_MANDOR_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR_PEKERJA.codePekerja')
		            ->select('EWS_PEKERJA.namaPekerja as nama', 'EWS_PEKERJA.codePekerja as code')
		            ->where('EWS_MANDOR_PEKERJA.codeMandor', '=', $request->mandor)
		            ->where('EWS_MANDOR_PEKERJA.AccMonth', '=', $bln_tukang)
		            ->where('EWS_MANDOR_PEKERJA.AccYear', '=', $thn_tukang)
		            ->orderBy('nama', 'asc')
		            ->get());
		        if (empty($tukang)) {
		            # code...
		            return $this->errMessage(400,'Tidak ada data tukang');
		        }

		        // $date = now();
		        $tgl_ubah = date_format($tgl, 'Y-m-d');

		        $user2[0]['rkhDate'] = $tgl_ubah;
            }

            # membuat identitas user
	        unset($user2[0]['codePekerja']);
	        unset($identitasPekerja['idRole']);
	        $user2[0]['sensus'] = $request->sensus;
	        $user2[0]['identitasPekerja'] = $identitasPekerja;
	        $user2[0]['identitasPekerja']['detailRole'] = $detailRole;

	        if ($request->sensus == FALSE) {
	        	$user2[0]['identitasPekerja']['detailPekerja']['codeMandor'] = $request->mandor;
	        	$namaMandor = $this->removeWhitespace2(DB::table('EWS_PEKERJA')
		            ->join('EWS_MANDOR', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR.codePekerja')
		            ->select('EWS_PEKERJA.namaPekerja as namaMandor')
		            ->where('EWS_MANDOR.codeMandor', '=', $request->mandor)
		            ->first());
	        	$user2[0]['identitasPekerja']['detailPekerja']['codeMandor'] = $request->mandor;
	        	$user2[0]['identitasPekerja']['detailPekerja']['namaMandor'] = $namaMandor['namaMandor'];
	        	# code...
		        $pilihTukang = array(
		            'id' => '',
		            'nama' => 'Pilih Pekerja',
		            'code' => ''
		        );
		        array_unshift($tukang, $pilihTukang); #inserts new elements into beginning of array
	        	$user2[0]['identitasPekerja']['detailPekerja']['tukang'] = $tukang;
	        }

	        ####################################GET ALL DATA###################################################

	        if ($request->sensus == FALSE) {
	        	return $this->spiMandor($user2, $tgl_ubah);
	        }
	        else {
	        	return $this->spiSensus($user2);
	        }
	    }

	    public function spiMandor($user2, $tgl_ubah)
	    {
	    	$user2[0]['RKM'] = array();
	        # rencana kerjaan harian
	        $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
	            ->join('EWS_SUB_JOB', 'EWS_JADWAL_RKM.codeAlojob', '=', 'EWS_SUB_JOB.subJobCode')
	            ->join('EWS_JOB', 'EWS_JOB.jobCode', '=', 'EWS_SUB_JOB.jobCode')
	            ->select('EWS_JADWAL_RKM.*', 'EWS_JOB.jobCode as parentJobCode', 'EWS_JOB.Description as parentJobName', 'EWS_SUB_JOB.subJobCode as childJobCode', 'EWS_SUB_JOB.Description as childJobName')
	            ->whereBetween('EWS_JADWAL_RKM.rkhDate', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
	            ->where('EWS_JADWAL_RKM.mandorCode', '=', $user2[0]['identitasPekerja']['detailPekerja']['codeMandor'])
	            ->get());
	        if (empty($rkm2)) {
	            # code...
	            return $this->errMessage(400,'Tidak ada RKM untuk tanggal '.date_format($tgl, 'd-m-Y'));
	        }
	        foreach ($rkm2 as $key_rkm => $rkm) {
	            # code...
	            $date = date_create($rkm['rkhDate']);
	            unset($rkm2[$key_rkm]['rkhDate']);
	            # tanggal bulan tahun
	            $rkm2[$key_rkm]['rkhDate'] = date_format($date, 'd F Y');
	            # jam:menit:detik
	            $rkm2[$key_rkm]['rkhTime'] = date_format($date, 'H:i:s');

	            $rkmListBlok[] = str_replace('.', '-', $rkm['codeBlok']);
	            $rkmListBlok = array_unique($rkmListBlok);
        		$rkmListBlok = array_values($rkmListBlok);

	            $rkmListSubJob[] = str_replace('.', '-', $rkm['childJobCode']);
	            $rkmListSubJob = array_unique($rkmListSubJob);
        		$rkmListSubJob = array_values($rkmListSubJob);
	        }

	        $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());
	        if (empty($job2)) {
	            # code...
	            return $this->errMessage(400,'Aktifitas tidak ditemukan');
	        }

	        $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->whereIn('subJobCode', $rkmListSubJob)->get());
	        if (empty($subJob2)) {
	            # code...
	            return $this->errMessage(400,'Sub-Aktifitas tidak ditemukan');
	        }
	        foreach ($subJob2 as $key => $value) {
	            # code...
	            $subJob2[$key]['Description'] = rtrim(preg_replace('/- [A-Z]{3}\/[A-Z]{3}/', '', $value['Description']));
	        }

	        $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok')
	            ->distinct('codeBlok')
	            ->whereIn('codeBlok', $rkmListBlok)
	            ->orderBy('codeBlok', 'asc')
	            ->get());
	        if (empty($listBlok2)) {
	            # code...
	            return $this->errMessage(400,'List blok tidak ditemukan');
	        }

	        $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok', 'plot')
	            ->distinct('codeBlok')
	            ->whereIn('codeBlok', $rkmListBlok)
	            ->orderBy('plot', 'asc')
	            ->get());
	        if (empty($listPlot2)) {
	            # code...
	            return $this->errMessage(400,'List plot tidak ditemukan');
	        }

	        $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok','plot', 'baris')
	            ->distinct('baris')
	            ->whereIn('codeBlok', $rkmListBlok)
	            ->orderBy('baris', 'asc')
	            ->get());
	        if (empty($listBaris2)) {
	            # code...
	            return $this->errMessage(400,'List baris tidak ditemukan');
	        }

	        $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->selectRaw('codeTanaman as code, codeBlok, plot, baris, noTanam, PlantingDate, DATEDIFF(wk, PlantingDate,GETDATE()) as week, 0 as status')
	            ->whereIn('codeBlok', $rkmListBlok)
	            ->orderBy('codeTanaman', 'asc')
	            ->get());
	        if (empty($listPokok2)) {
	            # code...
	            return $this->errMessage(400,'List pokok tidak ditemukan');
	        }

	        $trans_spi2 = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_MANDOR')
	            ->select('id', 'subJobCode', 'codeTanaman', 'rkhCode')
	            ->where('userid', '=', $user2[0]['id'])
	            // ->whereBetween('created_at', [$tgl_ubah, $tgl_ubah.' 23:59:59.000'])
	            ->get());

	        $newRKM = array();
	        foreach ($rkm2 as $key => $value) {
	            # code...
	            $newList = array(
	                'rkhCode' => $value['rkhCode'],
	                'blok' => $value['codeBlok'],
	                'rowStart' => $value['barisStart'],
	                'rowEnd' => $value['barisEnd']
	            );
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['parentJobCode'] = $value['parentJobCode'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['parentJobName'] = $value['parentJobName'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['childJobCode'] = $value['childJobCode'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['childJobName'] = $value['childJobName'];
	            $newRKM[$value['parentJobCode'].'_'.$value['childJobCode']]['listBlok'][] = $newList;
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhCode'] = $value['rkhCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['mandorCode'] = $value['mandorCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['codeAlojob'] = $value['codeAlojob'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobCode'] = $value['parentJobCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobName'] = $value['parentJobName'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobCode'] = $value['childJobCode'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobName'] = $value['childJobName'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhDate'] = $value['rkhDate'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhTime'] = $value['rkhTime'];
	            // $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['listBlok'][] = $newList;
	        }
	        $rkm2 = $newRKM;

	        foreach ($rkm2 as $key_rkm => $rkm) {
	            # code...
	            foreach ($subJob2 as $key_sj => $subJob) {
	                # code...
	                if (isset($rkm['childJobCode'])) {
	                    # code...
	                    if ($subJob['subJobCode'] == $rkm['childJobCode']) {
	                        # code...
	                        unset($rkm['parentJobCode']);
	                        unset($rkm['parentJobName']);
	                        unset($rkm['childJobCode']);
	                        unset($rkm['childJobName']);
	                        // unset($rkm['codeBlok']);
	                        // unset($rkm['rowStart']);
	                        // unset($rkm['rowEnd']);
	                        // array_push($subJob2[$key_sj], $rkm);
	                        $subJob2[$key_sj] = array_merge($subJob,$rkm);
	                    }
	                }
	            }
	        }

	        # MASUKIN PLOT-BARIS-POKOK ke dalam BLOK
	        for ($a=0; $a < count($listBlok2); $a++) { 
	        	$dataBlok[$a] = $listBlok2[$a];
	        	for ($b=0; $b < count($listPlot2); $b++) {
	        		if ($dataBlok[$a]['codeBlok'] == $listPlot2[$b]['codeBlok']) {
	        		 	$dataBlok[$a]['listPlot'][$b] = $listPlot2[$b];
		        		for ($c=0; $c < count($listBaris2); $c++) { 
		        			if 	(($dataBlok[$a]['listPlot'][$b]['plot'] == $listBaris2[$c]['plot']) && 
		        				($dataBlok[$a]['listPlot'][$b]['codeBlok'] == $listBaris2[$c]['codeBlok'])) {
			        		 	$dataBlok[$a]['listPlot'][$b]['listBaris'][$c] = $listBaris2[$c];
			        			for ($d=0; $d < count($listPokok2); $d++) { 
				        			if	(($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['codeBlok'] == $listPokok2[$d]['codeBlok']) &&
			        				 	($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['plot'] == $listPokok2[$d]['plot']) && 
				        				($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['baris'] == $listPokok2[$d]['baris'])) {
			        				 	$date = date_create($listPokok2[$d]['PlantingDate']);
                                        $listPokok2[$d]['date'] = date_format($date, 'd F Y');
                                        unset($listPokok2[$d]['PlantingDate']);
										unset($listPokok2[$d]['date']);
					        		 	$dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'][$d] = $listPokok2[$d];

				      //   				$allPokokPlot = $listPokok2[$d];
				      //   				unset($allPokokPlot['codeBlok']);
										// unset($allPokokPlot['plot']);
										// unset($allPokokPlot['baris']);
										// unset($allPokokPlot['noTanam']);
										// unset($allPokokPlot['PlantingDate']);
										// unset($allPokokPlot['week']);
										// unset($allPokokPlot['status']);
										// unset($allPokokPlot['date']);
				      //   		 		$dataBlok[$a]['listPlot'][$b]['listAllPokokPlot'][] = $allPokokPlot;
				        			}
			        			}
		        				$dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok']);
		        			}
		        		}
		        		$dataBlok[$a]['listPlot'][$b]['listBaris'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris']);
        		 	} 
	        	}
        		$dataBlok[$a]['listPlot'] = array_values($dataBlok[$a]['listPlot']);
	        }

	        # MASUKIN PLOT-BARIS-POKOK
	        foreach ($subJob2 as $key_sj => $subJob) { #Plot
	            # code...
                foreach ($subJob['listBlok'] as $key_lb => $listBlok) {
                    # code...
                    foreach ($dataBlok as $key_db => $db) {
                    	# code...
                    	if ($listBlok['blok'] == $db['codeBlok']) {
                    		# code...
                            if (($listBlok['rowStart'] == 0) && ($listBlok['rowEnd'] == 0)) {
                    			# code...
                    			// $subJob2[$key_sj]['listBlok'][$key_lb]['listPlot'] = $db['listPlot'];
                    			$listPlot = $db['listPlot'];
                    		} else {
	                    		foreach ($db['listPlot'] as $key_plot => $plot) {
	                    			# code...
	                    			foreach ($plot['listBaris'] as $key_baris => $baris) {
	                    				# code...
	                                    if ($baris['baris'] >= $listBlok['rowStart']  && $baris['baris'] <= $listBlok['rowEnd']) {
	                    					# do nothing
	                    				}else {
	                    					unset($dataBlok[$key_db]['listPlot'][$key_plot]['listBaris'][$key_baris]);
	                    				}
	                    			}
                    				$listPlot[] = $dataBlok[$key_db]['listPlot'][$key_plot];
	                    		}
                    		}
                    		$lpDone = 0;
                    		$lpNDone = 0;
                    		foreach ($listPlot as $key_plot => $plot) {
                    			# code...
                    			$lbDone = 0;
	                            $lbNDone = 0;
                    			foreach ($plot['listBaris'] as $key_baris => $baris) {
                    				# code...
                    				$lpkDone = 0;
                                    $lpkNDone = 0;
                    				foreach ($baris['listPokok'] as $key_pokok => $pokok) {
                    					# code...
			                    		foreach ($trans_spi2 as $key_tm => $trans_spi) {
			                                # code...
			                                if (($trans_spi['rkhCode'] == $listBlok['rkhCode']) && 
			                                ($trans_spi['codeTanaman'] == $pokok['code']) &&
			                                ($trans_spi['subJobCode'] == $subJob['subJobCode'])) {
			                                    # code...
			                                    // return 'ada';
			                                    // $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][$key_lpk]['status'] = 1;
			                                    $listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['status'] = 1;
			                                }
			                            }
			                            if ($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['status'] == 1) {
			                            	# code...
		                                	$lpkDone++;
			                            }else {
		                                	$lpkNDone++;
		                                }
										unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['week']);
		                                unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['codeBlok']);
		                                unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['plot']);
		                                unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['baris']);
		                                unset($listPlot[$key_plot]['listBaris'][$key_baris]['listPokok'][$key_pokok]['noTanam']);
			                            // $allPokokPlot = $listPokok2[$d];
				        				unset($pokok['codeBlok']);
										unset($pokok['plot']);
										unset($pokok['baris']);
										unset($pokok['noTanam']);
										unset($pokok['PlantingDate']);
										unset($pokok['status']);
										unset($pokok['date']);
				        		 		$listPlot[$key_plot]['listAllPokokPlot'][] = $pokok;
                    				}
		                            unset($listPlot[$key_plot]['listBaris'][$key_baris]['codeBlok']);
		                            unset($listPlot[$key_plot]['listBaris'][$key_baris]['plot']);

                    				$listPlot[$key_plot]['listBaris'][$key_baris]['pokokDone'] = $lpkDone;
                                    $listPlot[$key_plot]['listBaris'][$key_baris]['pokokNDone'] = $lpkNDone;
                                    $this->move_to_top($listPlot[$key_plot]['listBaris'][$key_baris], 'pokokNDone');
                                    $this->move_to_top($listPlot[$key_plot]['listBaris'][$key_baris], 'pokokDone');
                                    $this->move_to_top($listPlot[$key_plot]['listBaris'][$key_baris], 'baris');

                                    $lbDone+=$lpkDone;
                                    $lbNDone+=$lpkNDone;
                    			}
	                           	unset($listPlot[$key_plot]['codeBlok']);

                				$listPlot[$key_plot]['rowDone'] = $lbDone;
                                $listPlot[$key_plot]['rowNDone'] = $lbNDone;
                                $this->move_to_top($listPlot[$key_plot], 'rowNDone');
                                $this->move_to_top($listPlot[$key_plot], 'rowDone');
                                $this->move_to_top($listPlot[$key_plot], 'plot');
                				$subJob2[$key_sj]['listBlok'][$key_lb]['listPlot'] = $listPlot;

                                $lpDone+=$lbDone;
                                $lpNDone+=$lbNDone;
                    		}
            				$subJob2[$key_sj]['listBlok'][$key_lb]['plotDone'] = $lpDone;
	                        $subJob2[$key_sj]['listBlok'][$key_lb]['plotNDone'] = $lpNDone;
	                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lb], 'plotNDone');
	                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lb], 'plotDone');
	                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lb], 'blok');
	                        unset($listPlot);
                    	}
                    }
                }
	        }
	        # MASUKIN BLOK-PLOT-BARIS-TANAM

	        foreach ($job2 as $key_j => $job) {
	            # code...
	            foreach ($subJob2 as $key_sj => $subJob) {
	                # code...
	                $parentJob = '';
	                switch ($job['Description']) {
	                    case 'PLANT CARE':
	                        $parentJob = 'plantCare';
	                        break;

	                    case 'FRUIT CARE':
	                        $parentJob = 'fruitCare';
	                        break;

	                    case 'PANEN':
	                        $parentJob = 'panen';
	                        break;
	                    case 'PACKING HOUSE':
	                        $parentJob = 'packingHouse';
	                        break;
	                }
	                $job2[$key_j][$parentJob]['jobCode'] = $job['jobCode'];
	                $jobdesc = ucwords(strtolower($job['Description']));
	                $job2[$key_j][$parentJob]['jenisPekerjaan'] = $jobdesc;
	                if ($subJob['jobCode'] == $job['jobCode']) {
	                    # code...
	                    unset($subJob['codeAlojob']);
	                    unset($subJob['rkhTime']);
	                    $job2[$key_j][$parentJob]['listChildJob'][] = $subJob;
	                }
	            }
	            unset($job2[3]); # menghapus packing house
	            unset($job2[$key_j]['jobCode']);
	            unset($job2[$key_j]['Description']);
	        }

	        $user2[0]['RKM'] = $job2;

	        return $user2;
	    }

	    public function spiSensus($user2)
	    {
	    	$listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	    		->selectRaw('codeTanaman as code, codeBlok as blok, plot, baris, noTanam, DATEDIFF(wk, PlantingDate,GETDATE()) as week')
	    		->whereRaw('DATEDIFF(wk, PlantingDate,GETDATE()) % 4 = 0')
	            ->get());
	        if (empty($listPokok2)) {
	            # code...
	            return $this->errMessage(400,'Minggu ini tidak ada pokok yang di sensus '.date_format($tgl, 'd-m-Y'));
	        }
	        $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok as blok','plot', 'baris')
	            ->distinct('baris')
	    		->whereRaw('DATEDIFF(wk, PlantingDate,GETDATE()) % 4 = 0')
	            ->orderBy('baris', 'asc')
	            ->get());
	        if (empty($listBaris2)) {
	            # code...
	            return $this->errMessage(400,'List baris tidak ditemukan');
	        }
	        $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok as blok', 'plot')
	            ->distinct('codeBlok as blok')
	    		->whereRaw('DATEDIFF(wk, PlantingDate,GETDATE()) % 4 = 0')
	            ->orderBy('plot', 'asc')
	            ->get());
	        if (empty($listPlot2)) {
	            # code...
	            return $this->errMessage(400,'List plot tidak ditemukan');
	        }
	    	$listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
	            ->select('codeBlok as blok')
	            ->distinct('codeBlok as blok')
	    		->whereRaw('DATEDIFF(wk, PlantingDate,GETDATE()) % 4 = 0')
	            ->orderBy('codeBlok', 'asc')
	            ->get());
	        if (empty($listBlok2)) {
	            # code...
	            return $this->errMessage(400,'List blok tidak ditemukan');
	        }
	    	
	    	for ($a=0; $a < count($listBlok2); $a++) { 
	        	$dataBlok[$a] = $listBlok2[$a];
	        	for ($b=0; $b < count($listPlot2); $b++) {
	        		if ($dataBlok[$a]['blok'] == $listPlot2[$b]['blok']) {
	        		 	$dataBlok[$a]['listPlot'][$b] = $listPlot2[$b];
		        		for ($c=0; $c < count($listBaris2); $c++) { 
		        			if 	(($dataBlok[$a]['listPlot'][$b]['plot'] == $listBaris2[$c]['plot']) && 
		        				($dataBlok[$a]['listPlot'][$b]['blok'] == $listBaris2[$c]['blok'])) {
			        		 	$dataBlok[$a]['listPlot'][$b]['listBaris'][$c] = $listBaris2[$c];
			        			for ($d=0; $d < count($listPokok2); $d++) { 
			        				$pokok = $listPokok2[$d];
				        			if	(($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['blok'] == $listPokok2[$d]['blok']) &&
			        				 	($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['plot'] == $listPokok2[$d]['plot']) && 
				        				($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['baris'] == $listPokok2[$d]['baris'])) {
					        		 	unset($pokok['blok']);
		                                unset($pokok['plot']);
		                                unset($pokok['baris']);
		                                unset($pokok['noTanam']);
		                                $week = $pokok['week'];
										unset($pokok['week']);
					        		 	$dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'][$d] = $pokok;

		                                $pokok['week'] = $week;
				        				$allPokokPlot = $pokok;
				        				unset($allPokokPlot['blok']);
										unset($allPokokPlot['plot']);
										unset($allPokokPlot['baris']);
										unset($allPokokPlot['noTanam']);
										unset($allPokokPlot['status']);
										unset($allPokokPlot['date']);
				        		 		$dataBlok[$a]['listPlot'][$b]['listAllPokokPlot'][] = $allPokokPlot;
				        			}
			        			}
		        				unset($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['blok']);
                                unset($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['plot']);
		        				$dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris'][$c]['listPokok']);
		        			}
		        		}
		        		unset($dataBlok[$a]['listPlot'][$b]['blok']);
		        		$dataBlok[$a]['listPlot'][$b]['listBaris'] = array_values($dataBlok[$a]['listPlot'][$b]['listBaris']);
        		 	} 
	        	}
        		$dataBlok[$a]['listPlot'] = array_values($dataBlok[$a]['listPlot']);
	        }

    		$user2[0]['RKM'][0] = array(
    			'sensus' => array(
    				'jobCode' => '005',
    				'jenisPekerjaan' => 'Sensus',
    				'listChildJob' => array(
    					array(
    						'subJobCode' => '1',
    						'jobCode' => '005',
    						'Description' => 'Sensus',
    						'listBlok' => $dataBlok
    					)
    				)
    			)
    		);
    		return $user2;
	    }

	    public function storeSPI(Request $request)
	    {
	    	$validator = Validator::make($request->all(), [
                'sensus' => 'required|boolean'
            ]);
            if ($validator->fails()) {
                return $this->errMessage(400,$validator->messages()->first());
            }

            if ($request->sensus == TRUE) { # INPUT SENSUS
	            return $this->storeSPISensus($request);
	        }

            else { # INPUT BUKAN SENSUS
	            return $this->storeSPIMandor($request);
	        }
	    }

	    private function storeSPISensus(Request $request)
	    {
	    	$validator = Validator::make($request->all(), [
	            'codeTanaman' => 'required|between:0,20',
	            'week' => 'required|integer',
	            'girth' => 'required',
	            'totalLeaf' => 'required|integer',
	            'note' => 'nullable|between:0,255',
	            'dueDate' => 'required|date_format:d-m-Y',
	            'tanggal' => 'required|date',
	            'waktu' => 'required|date_format:H:i',
	            'userid' => 'required',
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

        	$request->girth = str_replace(',', '.', $request->girth);

	        $created_at = date_create($request->tanggal.' '.$request->waktu);
	        $created_at = date_format($created_at, 'Y-m-d H:i:s.B');

	        $dueDate = date_create($request->dueDate);
	        $dueDate = date_format($dueDate, 'Y-m-d');

	        $data = array(
	                'codeTanaman' => $request->codeTanaman,
	                'week' => $request->week,
	                'girth' => $request->girth,
	                'jumlahDaun' => $request->totalLeaf,
	                'corrActSPI' => $request->note,
	                'dueDate' => $dueDate,
	                'created_atSPI' => $created_at,
	                'useridSPI' => $request->userid,
	            );

	        $check = DB::table('EWS_TRANS_SPI_SENSUS')
	        			->where('codeTanaman', $request->codeTanaman)
	        			->where('week', $request->week)
	        			->value('id');
	        if (empty($check)) {
	            # code...
	            try {
	                DB::table('EWS_TRANS_SPI_SENSUS')->insert($data);
	                $message['message'][] = 'Data berhasil di input';
	                $message['message'][] = $data;
	            } catch (\Exception  $e) {
	                $message['message'][] = $e->getMessage();
	            }
	        }else{
	            try {
	                DB::table('EWS_TRANS_SPI_SENSUS')->where('id', $check)->update($data);
	                $message['message'][] = 'Data berhasil di update';
	                $message['message'][] = $data;
	            } catch (\Exception  $e) {
	                $message['message'][] = $e->getMessage();
	            }
	        }

	        return response()->json($message, 200);
	    }

	    private function storeSPIMandor(Request $request)
	    {
	    	$validator = Validator::make($request->all(), [
	            'codeRKH' => 'required|between:0,25',
	            'subJobCode' => 'required|between:0,15',
	            'userid' => 'required',
	            'codeMandor' => 'required|between:0,20',
	            'codeTukang' => 'required|between:0,20',
	            'codeTanaman' => 'required|between:0,20',
	            'note' => 'nullable|between:0,255',
	            'totalHand' => 'nullable|integer',
	            'totalFinger' => 'nullable|integer',
	            'totalLeaf' => 'nullable|integer',
	            'ribbonColor' => 'nullable|between:0,10',
	            'skimmingSize' => 'nullable',
	            'tanggal' => 'required|date',
	            'waktu' => 'required|date_format:H:i',
	            'pokokAwal' => 'nullable|integer',
	            'pokokAkhir' => 'nullable|integer'
	        ]);

	        if ($validator->fails()) {
	            return $this->errMessage(400,$validator->messages()->first());
	        }

	        if (isset($request->skimmingSize)) {
	        	# code...
	        	$request->skimmingSize = str_replace(',', '.', $request->skimmingSize);
	        }

	        $date = date_create($request->tanggal.' '.$request->waktu);
	        # tanggal bulan tahun
	        $created_at = date_format($date, 'Y-m-d H:i:s.B');

	        if ((isset($request->pokokAwal) && !empty($request->pokokAwal)) && 
	            ((isset($request->pokokAkhir) && !empty($request->pokokAkhir)))) {
	            $aw = $request->pokokAwal;
	            $ak = $request->pokokAkhir;

	            $message = array(
	                'code' => 200,
	                'message' => []
	            );

	            while ($aw <= $ak) {
	                $aw = str_pad($aw, 3, "0", STR_PAD_LEFT); # nambahin angka 0 di setiap digit satuan
	                $codeTanam = substr($request->codeTanaman, 0, strrpos($request->codeTanaman, '.')).'.'.$aw; #replace code pokok akhir dengan pokok skrg

	                # check data if exist
					$array = array(
	                        'rkhCode' => $request->codeRKH,
	                        'subJobCode' => $request->subJobCode,
	                        'userid' => $request->userid,
	                        'codeMandor' => $request->codeMandor,
	                        'codeTanaman' => $codeTanam
	                    );
					$check = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_MANDOR')
						->select('*')
						->where($array)
	                    ->get());
			        if (!empty($check)) 
			        {
	                    $message['message'][] = 'Data duplikat||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$codeTanam;
			        }
			        else
			        {
	                    $codeBlok = $this->removeWhitespace2(DB::table('EWS_LOK_TANAMAN')
	                        ->select('codeBlok')
	                        ->where('codeTanaman', '=', $codeTanam)
	                        ->first());
	                    if (empty($codeBlok)) {
	                        $message['message'][] = 'Data pokok tidak ada ||'.$codeTanam;
	                    }
	                    else{
	    		        	DB::table('EWS_TRANS_SPI_MANDOR')->insert([
	    	                    'rkhCode' => $request->codeRKH,
	    	                    'subJobCode' => $request->subJobCode,
	    	                    'userid' => $request->userid,
	    	                    'codeMandor' => $request->codeMandor,
	    	                    'codeTukang' => $request->codeTukang,
	                            'codeBlok' => $codeBlok['codeBlok'],
	    	                    'codeTanaman' => $codeTanam,
	    	                    'spiNote' => $request->note,
	    	                    'totalHand' => $request->totalHand,
	    	                    'totalFinger' => $request->totalFinger,
	    	                    'totalLeaf' => $request->totalLeaf,
	    	                    'ribbonColor' => $request->ribbonColor,
	    	                    'skimmingSize' => $request->skimmingSize,
	    	                    'created_at' => $created_at
	                        ]);
	                        $message['message'][] = 'Data berhasil di input||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$codeTanam;
	                    }
			        }
	                $aw++;
	            }
	            return response()->json($message, 200);
	        }
	        else
	        {
	            # check data if exist
	            $message = array(
	                'code' => 200,
	                'message' => []
	            );
				$array = array(
		                    'rkhCode' => $request->codeRKH,
		                    'subJobCode' => $request->subJobCode,
		                    'userid' => $request->userid,
	                        'codeMandor' => $request->codeMandor,
		                    'codeTanaman' => $request->codeTanaman
		                );
				$check = $this->removeWhitespace(DB::table('EWS_TRANS_SPI_MANDOR')
					->select('*')
					->where($array)
		            ->get());
		        if (!empty($check)) 
		        {
	                $message['message'][] = 'Data duplikat||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$request->codeTanaman;
		        }
		        else
		        {
	                $codeBlok = $this->removeWhitespace2(DB::table('EWS_LOK_TANAMAN')
	                    ->select('codeBlok')
	                    ->where('codeTanaman', '=', $request->codeTanaman)
	                    ->first());
	                if (empty($codeBlok)) {
	                    $message['message'][] = 'Data pokok tidak ada ||'.$request->codeTanaman;
	                }
	                else{
	    	            DB::table('EWS_TRANS_SPI_MANDOR')->insert([
	    	                'rkhCode' => $request->codeRKH,
	    	                'subJobCode' => $request->subJobCode,
	    	                'userid' => $request->userid,
    	                    'codeMandor' => $request->codeMandor,
	    	                'codeTukang' => $request->codeTukang,
	                        'codeBlok' => $codeBlok['codeBlok'],
	    	                'codeTanaman' => $request->codeTanaman,
	    	                'spiNote' => $request->note,
	    	                'totalHand' => $request->totalHand,
	    	                'totalFinger' => $request->totalFinger,
	    	                'totalLeaf' => $request->totalLeaf,
	    	                'ribbonColor' => $request->ribbonColor,
	    	                'skimmingSize' => $request->skimmingSize,
	    	                'created_at' => $created_at
	    	            ]);
	                    $message['message'][] = 'Data berhasil di input||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$request->codeTanaman;
	                }
	            }
	            return response()->json($message, 200);
	        }
	    }

	    public function getAllMandor()
	    {
	    	# code...
	        $mandor      = $this->removeWhitespace(DB::table('EWS_MANDOR')
	            ->join('EWS_PEKERJA', 'EWS_MANDOR.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
	        	->select('EWS_PEKERJA.namaPekerja as label', 'EWS_MANDOR.codeMandor as key')
	        	->orderBy('EWS_PEKERJA.namaPekerja', 'asc')
	        	->get());
	        return $mandor;
	    }
	# /SPI

    public function storePH_old(Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
                DB::table('EWS_PACKING_HOUSE')->insert([
                    'SubBlkHillCode' => $req->SubBlkHillCode,
                    'Tgl' => $create_at,
                    'EmpCode' => $req->EmpCode,
                    'HandClass' => $req->HandClass,
                    'CallHandClass2' => $req->CallHandClass2,
                    'CallHandClass4' => $req->CallHandClass4,
                    'CallHandClass6' => $req->CallHandClass6,
                    'CallHandClass7' => $req->CallHandClass7,
                    'CallHandClassAkhir' => $req->CallHandClassAkhir,
                    'FingerLen2' => $req->FingerLen2,
                    'FingerLen4' => $req->FingerLen4,
                    'FingerLen6' => $req->FingerLen6,
                    'FingerLen8' => $req->FingerLen8,
                    'FingerLen10' => $req->FingerLen10,
                    'FingerLenAkhir' => $req->FingerLenAkhir,
                    'FingerHand2' => $req->FingerHand2,
                    'FingerHand4' => $req->FingerHand4,
                    'FingerHand6' => $req->FingerHand6,
                    'FingerHand8' => $req->FingerHand8,
                    'FingerHand10' => $req->FingerHand10,
                    'FingerHandAkhir' => $req->FingerHandAkhir,
                    'Notes' => $req->Notes,
                    'userid' => $req->userid,
                ]);
        }

        return $request;
    }

    public function storeCT(Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
                DB::table('EWS_CEKLIST_TIMBANG')->insert([
                    'SubBlkHillCode' => $req->SubBlkHillCode,
                    'Tgl' => $create_at,
                    'NoBox' => $req->NoBox,
                    'ItemCode' => $req->ItemCode,
                    'Berat' => $req->Berat,
                    'Notes' => $req->Notes,
                    'userid' => $req->userid,
                ]);
        }

        return $request;
    }

    public function storeSENSUS(Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
                DB::table('EWS_SENSUS')->insert([
                    'SubBlkHillCode' => $req->SubBlkHillCode,
                    'Tgl' => $create_at,
                    'Girth' => $req->Girth,
                    'Kondisi' => $req->Kondisi,
                    'JmlDaun' => $req->JmlDaun,
                    'GlmNoGulma' => $req->GlmNoGulma,
                    'GlmNoSpray' => $req->GlmNoSpray,
                    'WtrNoGenang' => $req->WtrNoGenang,
                    'WtrParit' => $req->WtrParit,
                    'SucKenaTtkTumbuh' => $req->SucKenaTtkTumbuh,
                    'SucKenaLbgTimbun' => $req->SucKenaLbgTimbun,
                    'SucJmlSes' => $req->SucJmlSes,
                    'SucTngSucPlh' => $req->SucTngSucPlh,
                    'DelNoDaunSkt' => $req->DelNoDaunSkt,
                    'IriSlngKnnKiri' => $req->IriSlngKnnKiri,
                    'IriSlngTdkBocor' => $req->IriSlngTdkBocor,
                    'IriSlngTdkSumbat' => $req->IriSlngTdkSumbat,
                    'IriTnhLmbb' => $req->IriTnhLmbb,
                    'IriNoDaunPth' => $req->IriNoDaunPth,
                    'HamNoTrace' => $req->HamNoTrace,
                    'PpkRata' => $req->PpkRata,
                    'PpkAtasSerasah' => $req->PpkAtasSerasah,
                    'PpkTdkKenaDaun' => $req->PpkTdkKenaDaun,
                    'PpkJrkTepat' => $req->PpkJrkTepat,
                    'KrdlNoKrdl' => $req->KrdlNoKrdl,
                    'HillAkarTutup' => $req->HillAkarTutup,
                    'BITepatWaktu' => $req->BITepatWaktu,
                    'BITepatPosisi' => $req->BITepatPosisi,
                    'BagTepatWaktu' => $req->BagTepatWaktu,
                    'BagEmptPlhDrHand1' => $req->BagEmptPlhDrHand1,
                    'BagDiWiru' => $req->BagDiWiru,
                    'PrpCkpDalam' => $req->PrpCkpDalam,
                    'PrpTepatPosisi' => $req->PrpTepatPosisi,
                    'PrpTaliKencang' => $req->PrpTaliKencang,
                    'MrkTepatWaktu' => $req->MrkTepatWaktu,
                    'MrkTulisSesuai' => $req->MrkTulisSesuai,
                    'MrkJmlHand' => $req->MrkJmlHand,
                    'ForTdkAdaGanggu' => $req->ForTdkAdaGanggu,
                    'Notes' => $req->Notes,
                    'userid' => $req->userid,
                ]);
        }

        return $request;
    }

    public function move_to_top(&$array, $key) {
        $temp = array($key => $array[$key]);
        unset($array[$key]);
        $array = $temp + $array;
    }

    public function move_to_bottom(&$array, $key) {
        $value = $array[$key];
        unset($array[$key]);
        $array[$key] = $value;
    }

    public function removeWhitespace($arr)
    {
        if (!empty($arr)) {
            $arr = json_decode($arr,TRUE);
            foreach ($arr as $key => $value) {
                # code...
                $arr[$key] = array_map('rtrim',$arr[$key]);
                if (isset($arr[$key]['codeBlok'])) {
                    # code...
                    $arr[$key]['codeBlok'] = str_replace('-', '.', $arr[$key]['codeBlok']);
                }
                if (isset($arr[$key]['blok'])) {
                    # code...
                    $arr[$key]['blok'] = str_replace('-', '.', $arr[$key]['blok']);
                }
            }
            // $arr = json_encode($arr, JSON_PRETTY_PRINT);
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

    public function removeWhitespace3($arr)
    {
        if (!empty($arr)) {
            $arr = json_decode($arr,TRUE);
            // $arr = (array) $arr;
            $arr = array_map('rtrim',$arr);
    
            return $arr;
        } else {
            return false;
        }
    }

    /**
    * @param $interval
    * @param $datefrom
    * @param $dateto
    * @param bool $using_timestamps
    * @return false|float|int|string
    */
    public function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
    {
        /*
        $interval can be:
        yyyy - Number of full years
        q    - Number of full quarters
        m    - Number of full months
        y    - Difference between day numbers
               (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
        d    - Number of full days
        w    - Number of full weekdays
        ww   - Number of full weeks
        h    - Number of full hours
        n    - Number of full minutes
        s    - Number of full seconds (default)
        */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto   = strtotime($dateto, 0);
        }

        $difference        = $dateto - $datefrom; // Difference in seconds
        $months_difference = 0;

        switch ($interval) {
            case 'yyyy': // Number of full years
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                    $years_difference--;
                }

                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                    $years_difference++;
                }

                $datediff = $years_difference;
            break;

            case "q": // Number of full quarters
                $quarters_difference = floor($difference / 8035200);

                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $quarters_difference--;
                $datediff = $quarters_difference;
            break;

            case "m": // Number of full months
                $months_difference = floor($difference / 2678400);

                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $months_difference--;

                $datediff = $months_difference;
            break;

            case 'y': // Difference between day numbers
                $datediff = date("z", $dateto) - date("z", $datefrom);
            break;

            case "d": // Number of full days
                $datediff = floor($difference / 86400);
            break;

            case "w": // Number of full weekdays
                $days_difference  = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day        = date("w", $datefrom);
                $days_remainder   = floor($days_difference % 7);
                $odd_days         = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }

                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }

                $datediff = ($weeks_difference * 5) + $days_remainder;
            break;

            case "ww": // Number of full weeks
                $datediff = floor($difference / 604800);
            break;

            case "h": // Number of full hours
                $datediff = floor($difference / 3600);
            break;

            case "n": // Number of full minutes
                $datediff = floor($difference / 60);
            break;

            default: // Number of full seconds (default)
                $datediff = $difference;
            break;
        }

        return $datediff;
    }

    public function errMessage($code, $message)
    {
        # code...
        return response()->json([
            'errors' => [
                'code' => $code,
                'message' => $message
            ]
        ], $code);
    }
}
