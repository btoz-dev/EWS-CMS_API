<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Http\Response;

class AppController extends Controller
{
    
	public function test(Request $request)
	{
		# code...
		return DB::getDatabaseName();
		// return var_dump(Artisan::call('config:cache'));
	}

    public function getUser (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errMessage(400,$validator->messages()->first());
        }

        $user2 = $this->removeWhitespace(DB::table('users')
            ->select('id','name','username','password_decrypt as password','codePekerja')
            ->whereRaw('username = ? COLLATE Latin1_General_CS_AS', [$request->username])
            ->whereRaw('password_decrypt = ? COLLATE Latin1_General_CS_AS', [$request->password])
            ->get());
        if (empty($user2)) {
            # code...
            return $this->errMessage(400,'Username atau Password salah.');
        }

        $identitasPekerja = $this->removeWhitespace2(DB::table('EWS_PEKERJA')
            ->join('users', 'users.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
            ->select('namaPekerja as nama', 'idRole')
            ->where('EWS_PEKERJA.codePekerja', '=', $user2[0]['codePekerja'])
            ->first());
        if (empty($identitasPekerja)) {
            # code...
            return $this->errMessage(400,'Tidak ada data pekerja');
        }

        $detailRole = $this->removeWhitespace2(DB::table('EWS_ROLE_USER')
            ->select('id', 'namaRole as nama', 'descRole as desc')
            ->where('id', '=', $identitasPekerja['idRole'])
            ->first());
        if (empty($detailRole)) {
            # code...
            return $this->errMessage(400,'Tidak ada data role');
        }
        
        if ($detailRole['id'] == self::ID_ROLE_MANDOR) {#8
            # code...
            return $this->getRKMMandor($user2, $identitasPekerja, $detailRole);
        }

        if ($detailRole['id'] == 7) {
            # code...
            return $this->getRKMKawil($user2, $identitasPekerja, $detailRole);
        }
    }

    public function getRKMMandor ($user2, $identitasPekerja, $detailRole)
    {
    	$codeMandor = $this->removeWhitespace2(DB::table('EWS_MANDOR')
            ->select('codeMandor')
            ->where('codePekerja', '=', $user2[0]['codePekerja'])
            ->first());
        if (empty($codeMandor)) {
            # code...
            return $this->errMessage(400,'Tidak ada data kode mandor');
        }

        $tukang = $this->removeWhitespace(DB::table('EWS_PEKERJA')
            ->join('EWS_MANDOR_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR_PEKERJA.codePekerja')
            ->select('EWS_MANDOR_PEKERJA.id', 'EWS_PEKERJA.namaPekerja as nama', 'EWS_PEKERJA.codePekerja as code')
            ->where('EWS_MANDOR_PEKERJA.codeMandor', '=', $codeMandor['codeMandor'])
            ->orderBy('nama', 'asc')
            ->get());
        if (empty($tukang)) {
            # code...
            return $this->errMessage(400,'Tidak ada data tukang');
        }

        $date = now();
        $tgl = date_create($date);
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
        $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
            ->orderBy('codeTanaman', 'asc')
            ->get());

        $newRKM = array();
        foreach ($rkm2 as $key => $value) {
            # code...
            $newList = array(
                'blok' => $value['codeBlok'],
                'rowStart' => $value['barisStart'],
                'rowEnd' => $value['barisEnd']
            );
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhCode'] = $value['rkhCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['mandorCode'] = $value['mandorCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['codeAlojob'] = $value['codeAlojob'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobCode'] = $value['parentJobCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobName'] = $value['parentJobName'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobCode'] = $value['childJobCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobName'] = $value['childJobName'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhDate'] = $value['rkhDate'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhTime'] = $value['rkhTime'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['listBlok'][] = $newList;
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
                        unset($rkm['codeBlok']);
                        unset($rkm['rowStart']);
                        unset($rkm['rowEnd']);
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
                if (!isset($subJob['rkhCode'])) {
                    # code...
                    unset($subJob2[$key_sj]);
                }
            }
        }

        # MASUKIN PLOT-BARIS-POKOK
        foreach ($subJob2 as $key_sj => $subJob) { #Plot
            # code...
            if (isset($subJob['rkhCode'])) {
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
            if (isset($subJob['rkhCode'])) {
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
            if (isset($subJob['rkhCode'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    # code...
                    if (isset($listBlok['listPlot'])) {
                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                            if (isset($listPlot['listBaris'])) {
                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                                    foreach ($listPokok2 as $key_lpk => $listPokok) {
                                        # code...
                                        if (($listBlok['blok'] == $listPokok['codeBlok']) && 
                                        ($listPlot['plot'] == $listPokok['plot']) && 
                                        ($listBaris['baris'] == $listPokok['baris'])) {
                                            # code...
                                            $listPokok['status'] = 0;
                                            $listPokok['jmlMinggu'] = $this->datediff('ww', $listPokok['PlantingDate'], now());
                                            unset($listPokok['Description']);
                                            unset($listPokok['codeBlok']);
                                            unset($listPokok['plot']);
                                            unset($listPokok['baris']);
                                            unset($listPokok['noTanam']);
                                            $date = date_create($listPokok['PlantingDate']);
                                            $listPokok['date'] = date_format($date, 'd F Y');
                                            unset($listPokok['PlantingDate']);
                                            $listPokok['code'] = $listPokok['codeTanaman'];
                                            unset($listPokok['codeTanaman']);
                                            $listPokok['week'] = $listPokok['jmlMinggu'];
                                            unset($listPokok['jmlMinggu']);
                                            $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][] = $listPokok;
                                            unset($listPokok['date']);
                                            unset($listPokok['status']);
                                            unset($listPokok['week']);
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
        # MASUKIN BLOK-PLOT-BARIS-TANAM

        # MASUKIN STATUS KE POKOK
        # tanaman sudah dikerjakan
        $trans_mandor2 = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
            ->select('id', 'subJobCode', 'codeTanaman', 'rkhCode')
            ->where('userid', '=', $user2[0]['id'])
            // ->whereBetween('created_at', [$tgl_ubah, $tgl_ubah.' 23:59:59.999'])
            ->get());
        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
            # code...
            if (isset($subJob['rkhCode'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                        foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                            foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
                                # code...
                                // return $listPokok;
                                foreach ($trans_mandor2 as $key_tm => $trans_mandor) {
                                    # code...
                                    if (($trans_mandor['rkhCode'] == $subJob['rkhCode']) && 
                                    ($trans_mandor['codeTanaman'] == $listPokok['code']) &&
                                    ($trans_mandor['subJobCode'] == $subJob['subJobCode'])) {
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
            if (isset($subJob['rkhCode'])) {
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
                                            }
                                            else{
                                                $lpkNDone++;
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
            unset($job2[$key_j]['jobCode']);
            unset($job2[$key_j]['Description']);
        }

        $user2[0]['RKM'] = $job2;

        return $user2;
    }

    public function getRKMKawil ($user2, $identitasPekerja, $detailRole)
    {
        unset($identitasPekerja['idRole']);
        $user2[0]['identitasPekerja'] = $identitasPekerja;
        $user2[0]['identitasPekerja']['detailRole'] = $detailRole;

        ####################################GET ALL DATA###################################################
        $user2[0]['RKM'] = array();
        $date = '07-02-2019'; # 13-02-2019||07-02-2019
        $tgl = date_create($date);
        $tgl_ubah = date_format($tgl, 'Y-m-d');

        # rencana kerjaan harian
        $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')
        	->join('EWS_SUB_JOB', 'EWS_JADWAL_RKM.codeAlojob', '=', 'EWS_SUB_JOB.subJobCode')
        	->join('EWS_JOB', 'EWS_JOB.jobCode', '=', 'EWS_SUB_JOB.jobCode')
        	->join('EWS_MANDOR', 'EWS_MANDOR.codeMandor', '=', 'EWS_JADWAL_RKM.mandorCode')
        	->join('EWS_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR.codePekerja')
        	->select('EWS_JADWAL_RKM.*', 'EWS_JOB.jobCode as parentJobCode', 'EWS_JOB.Description as parentJobName', 'EWS_SUB_JOB.subJobCode as childJobCode', 'EWS_SUB_JOB.Description as childJobName', 'EWS_PEKERJA.namaPekerja as namaMandor')
        	->join('EWS_TRANS_MANDOR', 'EWS_TRANS_MANDOR.rkhCode', '=', 'EWS_JADWAL_RKM.rkhCode')
        	->whereBetween('EWS_JADWAL_RKM.rkhDate', [$tgl_ubah, $tgl_ubah.' 23:59:59.999'])
        	->distinct()
        	->get());
        // return $rkm2;
        # tanaman sudah dikerjakan
        $trans_mandor2 = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
            ->select('id', 'subJobCode', 'codeTanaman')
            ->get());

        # tanaman sudah diverifikasi
        $transKawil = $this->removeWhitespace(DB::table('EWS_TRANS_KAWIL')
            ->get());

        $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());
        $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->get());

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            $date = date_create($rkm['rkhDate']);
            unset($rkm2[$key_rkm]['rkhDate']);
            # tanggal bulan tahun
            $rkm2[$key_rkm]['rkhDate'] = date_format($date, 'd F Y');
            # jam:menit:detik
            $rkm2[$key_rkm]['rkhTime'] = date_format($date, 'H:i:s');
        }

        $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('codeBlok')->groupBy('codeBlok')->get());
        $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('plot')->groupBy('plot')->get());
        $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('baris')->groupBy('baris')->get());
        // $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->get());
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
                        // array_push($subJob2[$key_sj], $rkm);
                        $subJob2[$key_sj] = array_merge($subJob,$rkm);
                    }
                }
            }
        }

        # MASUKIN BLOK-PLOT-BARIS-POKOK
        foreach ($subJob2 as $key_sj => $subJob) { #Blok
            # code...
            foreach ($listBlok2 as $key_lb => $listBlok) {
                # code...
                if (isset($subJob['codeBlok'])) {
                    # code...
                    if ($listBlok['codeBlok'] == $subJob['codeBlok']) {
                        # code...
                        $subJob2[$key_sj]['listBlok'][] = $listBlok;
                    }
                }
                else
                {
                    unset($subJob2[$key_sj]);
                }
            }
        }
        // return $subJob2;
        foreach ($subJob2 as $key_sj => $subJob) { #Plot
            # code...
            if (isset($subJob['codeBlok'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    # code...
                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'] = $listPlot2;
                }
            }
        }
        foreach ($subJob2 as $key_sj => $subJob) {#Baris
            # code...
            if (isset($subJob['codeBlok'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    if (isset($listBlok['listPlot'])) {
                        # code...
                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                            foreach ($listBaris2 as $key_lb => $listBaris) {
                                # code...
                                if ($listBaris['baris'] >= $subJob['rowStart']  && $listBaris['baris'] <= $subJob['rowEnd']) {
                                    # code...
                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][] = $listBaris;
                                }
                            }
                        }
                    }
                }
            }
        }

        $listPokok2 = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
            		// ->where('EWS_TRANS_MANDOR.rkhCode', '=', $subJob['rkhCode'])
            		#JOIN WHERE
					->join('EWS_LOK_TANAMAN', 'EWS_LOK_TANAMAN.codeTanaman', '=', 'EWS_TRANS_MANDOR.codeTanaman')
            		// ->where('EWS_LOK_TANAMAN.codeBlok', '=', $listBlok['codeBlok'])
            		// ->where('EWS_LOK_TANAMAN.plot', '=', $listPlot['plot'])
            		// ->where('EWS_LOK_TANAMAN.baris', '=', $listBaris['baris'])
            		#JOIN WHERE
					->join('EWS_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_TRANS_MANDOR.codeTukang')
					#SELECT
					->selectRaw('EWS_LOK_TANAMAN.*, EWS_PEKERJA.namaPekerja, EWS_TRANS_MANDOR.id as idTransMandor, 0 as status, EWS_TRANS_MANDOR.rkhCode, EWS_TRANS_MANDOR.totalHand, EWS_TRANS_MANDOR.totalFinger, EWS_TRANS_MANDOR.totalLeaf, EWS_TRANS_MANDOR.ribbonColor, EWS_TRANS_MANDOR.skimmingSize')
            		->get());
        // return $listPokok2;
        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
            # code...
            if (isset($subJob['codeBlok'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    # code...
                    if (isset($listBlok['listPlot'])) {
                        # code...
                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                            if (isset($listPlot['listBaris'])) {
                                # code...
                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                                	foreach ($listPokok2 as $key_lpk => $listPokok) {
                                		# code...
                                		if (($listBlok['codeBlok'] == $listPokok['codeBlok']) && 
                                         ($listPlot['plot'] == $listPokok['plot']) && 
                                         ($listBaris['baris'] == $listPokok['baris']) && 
                                     	 ($subJob['rkhCode'] == $listPokok['rkhCode'])) {
                                            # code...
                                            if (is_null($listPokok['totalHand'])) {
                                                # code...
                                                unset($listPokok['totalHand']);
                                            }
                                            if (is_null($listPokok['totalLeaf'])) {
                                                # code...
                                                unset($listPokok['totalLeaf']);
                                            }
                                            if (is_null($listPokok['totalFinger'])) {
                                                # code...
                                                unset($listPokok['totalFinger']);
                                            }
                                            if (is_null($listPokok['ribbonColor'])) {
                                                # code...
                                                unset($listPokok['ribbonColor']);
                                            }
                                            if (is_null($listPokok['skimmingSize'])) {
                                                # code...
                                                unset($listPokok['skimmingSize']);
                                            }
                                            // $listPokok['status'] = 0;
                                            unset($listPokok['rkhCode']);
                                            $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][] = $listPokok;
                                        }
                                	}
                                }
                            }
                        }
                    }
                }
            }
        }
        # MASUKIN BLOK-PLOT-BARIS-TANAM
        // return $subJob2;

        # MASUKIN STATUS KE POKOK
        $pokokTransKawil = $this->removeWhitespace(DB::table('EWS_TRANS_KAWIL')
                        		#JOIN WHERE
    							->join('EWS_TRANS_MANDOR', 'EWS_TRANS_MANDOR.id', '=', 'EWS_TRANS_KAWIL.idEWSTransMandor')
                        		// ->where('EWS_TRANS_KAWIL.idEWSTransMandor', '=', $listPokok['idTransMandor'])
                        		->get());
        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
            # code...
            if (isset($subJob['codeBlok']) && isset($subJob['listBlok'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    if (isset($listBlok['listPlot'])) {
                        # code...
                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                            if (isset($listPlot['listBaris'])) {
                                # code...
                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                                    if (isset($listBaris['listPokok'])) {
                                        # code...
                                        foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
                                            # code...
                                            foreach ($pokokTransKawil as $key_ptk => $ptk) {
                                            	# code...
                                            	if ($ptk['idEWSTransMandor'] == $listPokok['idTransMandor']) {
                                            		# code...
                                            		$subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][$key_lpk]['status'] = 1;
                                            	}
                                            }
                                        }
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
            if (isset($subJob['codeBlok']) && isset($subJob['listBlok'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    $lpDone = 0;
                    $lpNDone = 0;
                    if (isset($listBlok['listPlot'])) {
                        # code...
                        foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                            $lbDone = 0;
                            $lbNDone = 0;
                            if (isset($listPlot['listBaris'])) {
                                # code...
                                foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                                    $lpkDone = 0;
                                    $lpkNDone = 0;
                                    if (isset($listBaris['listPokok'])) {
                                        # code...
                                        foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
                                            # code...
                                            if ($listPokok['status'] == 1) {
                                                # code...
                                                $lpkDone++;
                                            }
                                            else{
                                                $lpkNDone++;
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
                                }
                                $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['barisDone'] = $lbDone;
                                $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['barisNDone'] = $lbNDone;
                                $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp], 'barisNDone');
                                $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp], 'barisDone');
                                $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp], 'plot');

                                $lpDone+=$lbDone;
                                $lpNDone+=$lbNDone;
                            }
                        }
                        $subJob2[$key_sj]['listBlok'][$key_lt]['plotDone'] = $lpDone;
                        $subJob2[$key_sj]['listBlok'][$key_lt]['plotNDone'] = $lpNDone;
                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'plotNDone');
                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'plotDone');
                        $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'codeBlok');
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
                }
                if ($subJob['jobCode'] == $job['jobCode']) {
                    # code...
                    $job2[$key_j][$parentJob]['jobCode'] = $job['jobCode'];
                    $jobdesc = ucwords(strtolower($job['Description']));
                    $job2[$key_j][$parentJob]['jenisPekerjaan'] = $jobdesc;
                    $job2[$key_j][$parentJob]['listChildJob'][] = $subJob;
                    // $job2[$key_j][$parentJob][] = $subJob;
                    // $job2[$key_j]['childJob'][] = $subJob;
                }
                // $job2[$key_j]['jenisPekerjaan'] = $job2[$key_j]['Description'];
            }
            unset($job2[$key_j]['jobCode']);
            unset($job2[$key_j]['Description']);
            // $this->move_to_top($job2[$key_j], 'jenisPekerjaan');
        }


        $user2[0]['RKM'] = $job2;

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
            'mandorNote' => 'nullable|between:0,255',
            'totalHand' => 'nullable|integer',
            'totalFinger' => 'nullable|integer',
            'totalLeaf' => 'nullable|integer',
            'ribbonColor' => 'nullable|between:0,10',
            'skimmingSize' => 'nullable|integer',
            'tanggal' => 'required',
            'waktu' => 'required',
            'pokokAwal' => 'nullable|integer',
            'pokokAkhir' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return $this->errMessage(400,$validator->messages()->first());
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
    	                    'mandorNote' => $request->mandorNote,
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
                // $message['message'][] = 'Data duplikat||'.$request->codeRKH.'||'.$request->subJobCode.'||'.$request->userid.'||'.$codeTanam;
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
    	                'mandorNote' => $request->mandorNote,
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
            return response()->json($message, 200);
        }
    }

    public function storeKawil (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idEWSTransMandor' => 'required',
            'kawilNote' => 'required|between:0,255',
            'userid' => 'required',
            'tanggal' => 'required',
            'waktu' => 'required',
        ]);

        // return $validator->messages()->first();
        if ($validator->fails()) {
            return $this->errMessage(400,$validator->messages()->first());
        }

        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $created_at = date_format($date, 'Y-m-d H:i:s.B');
        # 3. FRUIT CARE, MODEL MARKING
        # 4. PANEN, MODEL SKIMMING 
        if ((isset($request->pokokAwal) && !empty($request->pokokAwal)) && 
            ((isset($request->pokokAkhir) && !empty($request->pokokAkhir)))) {
            # 1. PLANTCARE, MODEL PEMUPUKAN
            # KHUSUS PEMUPUKAN BENTUK YANG DIBERIKAN BERUPA POKOK DARI NOMOR X KE NOMOR Y
            # JADI API GENERATE 
            # code...
            $aw = $request->pokokAwal;
            $ak = $request->pokokAkhir;
            while ($aw <= $ak) {
                # code...
                $aw = preg_replace('/(?<!\d)(\d)(?!\d)/', '0$1', $aw); # nambahin angka 0 di setiap digit satuan
                $codeTanam = preg_replace('/.[0-9]$/', $aw, $request->codeTanaman); #replace code pokok akhir dengan pokok skrg
                DB::table('EWS_TRANS_MANDOR')->insert([
                    'idEWSTransMandor' => $request->idEWSTransMandor,
                    'subJobCode' => $request->subJobCode,
                    'kawilNote' => $request->kawilNote,
                    'userid' => $request->userid,
                    'created_at' => $created_at
                ]);
                $aw++;
            }

            return 'Data telah di input';
        }
        else
        {
            # 2. FRUIT CARE, MODEL BI [NORMAL]
                # code...
            DB::table('EWS_TRANS_MANDOR')->insert([
                'idEWSTransMandor' => $request->idEWSTransMandor,
                'subJobCode' => $request->subJobCode,
                'kawilNote' => $request->kawilNote,
                'userid' => $request->userid,
                'created_at' => $created_at
            ]);
            return 'Data telah di input';
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

    public function storeBT(Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
                DB::table('EWS_BERAT_TANDAN')->insert([
                    'SubBlkHillCode' => $req->SubBlkHillCode,
                    'Tgl' => $create_at,
                    'EmpCode' => $req->EmpCode,
                    'BeratBruto' => $req->BeratBruto,
                    'BeratBonggol' => $req->BeratBonggol,
                    'Notes' => $req->Notes,
                    'userid' => $req->userid,
                ]);
        }

        return $request;
    }

    public function storePH(Request $request)
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

    public function storeCA(Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
                DB::table('EWS_CEKLIST_TIMBANG')->insert([
                    'SubBlkHillCode' => $req->SubBlkHillCode,
                    'Tgl' => $create_at,
                    'CorrectiveCat' => $req->CorrectiveCat,
                    'CorrAction' => $req->CorrAction,
                    'DueDate' => $req->DueDate,
                    'CorrDate' => $req->CorrDate,
                    'EmpCode' => $req->EmpCode,
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
        $arr = json_decode($arr,TRUE);
        foreach ($arr as $key => $value) {
            # code...
            $arr[$key] = array_map('rtrim',$arr[$key]);
            if (isset($arr[$key]['codeBlok'])) {
                # code...
                $arr[$key]['codeBlok'] = str_replace('-', '.', $arr[$key]['codeBlok']);
            }
        }
        // $arr = json_encode($arr, JSON_PRETTY_PRINT);
        return $arr;
    }

    public function removeWhitespace2($arr)
    {
        $arr = (array) $arr;
        $arr = array_map('rtrim',$arr);

        return $arr;
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

    public function getUser2 (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errMessage(400,$validator->messages()->first());
        }

        $user2 = $this->removeWhitespace(DB::table('users')
            ->select('id','name','username','password_decrypt as password','codePekerja')
            ->whereRaw('username = ? COLLATE Latin1_General_CS_AS', [$request->username])
            ->whereRaw('password_decrypt = ? COLLATE Latin1_General_CS_AS', [$request->password])
            ->get());
        if (empty($user2)) {
            # code...
            return $this->errMessage(400,'Incorrect username or password.');
        }

        $identitasPekerja = $this->removeWhitespace2(DB::table('EWS_PEKERJA')
            ->join('users', 'users.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
            ->select('namaPekerja as nama', 'idRole')
            ->where('EWS_PEKERJA.codePekerja', '=', $user2[0]['codePekerja'])
            ->first());
        if (empty($identitasPekerja)) {
            # code...
            return $this->errMessage(400,'No Data Pekerja');
        }

        $detailRole = $this->removeWhitespace2(DB::table('EWS_ROLE_USER')
            ->select('id', 'namaRole as nama', 'descRole as desc')
            ->where('id', '=', $identitasPekerja['idRole'])
            ->first());
        if (empty($detailRole)) {
            # code...
            return $this->errMessage(400,'No Data Role');
        }
        
        if ($detailRole['id'] == self::ID_ROLE_MANDOR) {#8
            # code...
            return $this->getRKMMandor2($user2, $identitasPekerja, $detailRole);
        }

        if ($detailRole['id'] == 7) {
            # code...
            return $this->getRKMKawil2($user2, $identitasPekerja, $detailRole);
        }

    }

    public function getRKMMandor2 ($user2, $identitasPekerja, $detailRole)
    {
        $codeMandor = $this->removeWhitespace2(DB::table('EWS_MANDOR')
            ->select('codeMandor')
            ->where('codePekerja', '=', $user2[0]['codePekerja'])
            ->first());
        if (empty($codeMandor)) {
            # code...
            return $this->errMessage(400,'No Data Code Mandor');
        }

        $tukang = $this->removeWhitespace(DB::table('EWS_PEKERJA')
            ->join('EWS_MANDOR_PEKERJA', 'EWS_PEKERJA.codePekerja', '=', 'EWS_MANDOR_PEKERJA.codePekerja')
            ->select('EWS_MANDOR_PEKERJA.id', 'EWS_PEKERJA.namaPekerja as nama', 'EWS_PEKERJA.codePekerja as code')
            ->where('EWS_MANDOR_PEKERJA.codeMandor', '=', $codeMandor['codeMandor'])
            ->orderBy('nama', 'asc')
            ->get());
        if (empty($tukang)) {
            # code...
            return $this->errMessage(400,'No Data Tukang');
        }

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
        $date = now();
        $tgl = date_create($date);
        $tgl_ubah = date_format($tgl, 'Y-m-d');
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
            return $this->errMessage(400,'No RKM');
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
        $listPokok2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')
            ->orderBy('codeTanaman', 'asc')
            ->get());

        $newRKM = array();
        foreach ($rkm2 as $key => $value) {
            # code...
            $newList = array(
                'blok' => $value['codeBlok'],
                'rowStart' => $value['barisStart'],
                'rowEnd' => $value['barisEnd']
            );
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhCode'] = $value['rkhCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['mandorCode'] = $value['mandorCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['codeAlojob'] = $value['codeAlojob'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobCode'] = $value['parentJobCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['parentJobName'] = $value['parentJobName'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobCode'] = $value['childJobCode'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['childJobName'] = $value['childJobName'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhDate'] = $value['rkhDate'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['rkhTime'] = $value['rkhTime'];
            $newRKM[$value['rkhCode'].'_'.$value['codeAlojob']]['listBlok'][] = $newList;
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
                        unset($rkm['codeBlok']);
                        unset($rkm['rowStart']);
                        unset($rkm['rowEnd']);
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
                if (!isset($subJob['rkhCode'])) {
                    # code...
                    unset($subJob2[$key_sj]);
                }
            }
        }

        # MASUKIN PLOT-BARIS-POKOK
        foreach ($subJob2 as $key_sj => $subJob) { #Plot
            # code...
            if (isset($subJob['rkhCode'])) {
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
            if (isset($subJob['rkhCode'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
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
        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
            # code...
            if (isset($subJob['rkhCode'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    # code...
                    foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                        foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                            foreach ($listPokok2 as $key_lpk => $listPokok) {
                                # code...
                                if (($listBlok['blok'] == $listPokok['codeBlok']) && 
                                 ($listPlot['plot'] == $listPokok['plot']) && 
                                 ($listBaris['baris'] == $listPokok['baris'])) {
                                    # code...
                                    $listPokok['status'] = 0;
                                    $listPokok['jmlMinggu'] = $this->datediff('ww', $listPokok['PlantingDate'], now());
                                    unset($listPokok['Description']);
                                    unset($listPokok['codeBlok']);
                                    unset($listPokok['plot']);
                                    unset($listPokok['baris']);
                                    unset($listPokok['noTanam']);
                                    $date = date_create($listPokok['PlantingDate']);
                                    $listPokok['date'] = date_format($date, 'd F Y');
                                    unset($listPokok['PlantingDate']);
                                    $listPokok['code'] = $listPokok['codeTanaman'];
                                    unset($listPokok['codeTanaman']);
                                    $listPokok['week'] = $listPokok['jmlMinggu'];
                                    unset($listPokok['jmlMinggu']);
                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['listPokok'][] = $listPokok;
                                    unset($listPokok['date']);
                                    unset($listPokok['status']);
                                    unset($listPokok['week']);
                                    $subJob2[$key_sj]['listBlok'][$key_lt]['listPlot'][$key_lp]['listAllPokokPlot'][] = $listPokok;
                                }
                            }
                        }
                    }
                }
            }
        }
        # MASUKIN BLOK-PLOT-BARIS-TANAM

        # MASUKIN STATUS KE POKOK
        # tanaman sudah dikerjakan
        $trans_mandor2 = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')
            ->select('id', 'subJobCode', 'codeTanaman', 'rkhCode')
            ->where('userid', '=', $user2[0]['id'])
            // ->whereBetween('created_at', [$tgl_ubah, $tgl_ubah.' 23:59:59.999'])
            ->get());
        foreach ($subJob2 as $key_sj => $subJob) {#Pokok
            # code...
            if (isset($subJob['rkhCode'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                        foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                            foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
                                # code...
                                // return $listPokok;
                                foreach ($trans_mandor2 as $key_tm => $trans_mandor) {
                                    # code...
                                    if (($trans_mandor['rkhCode'] == $subJob['rkhCode']) && 
                                        ($trans_mandor['codeTanaman'] == $listPokok['code']) &&
                                        ($trans_mandor['subJobCode'] == $subJob['subJobCode'])) {
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
            if (isset($subJob['rkhCode'])) {
                foreach ($subJob['listBlok'] as $key_lt => $listBlok) {
                    $lpDone = 0;
                    $lpNDone = 0;
                    foreach ($listBlok['listPlot'] as $key_lp => $listPlot) {
                        $lbDone = 0;
                        $lbNDone = 0;
                        foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                            $lpkDone = 0;
                            $lpkNDone = 0;
                            foreach ($listBaris['listPokok'] as $key_lpk => $listPokok) {
                                # code...
                                if ($listPokok['status'] == 1) {
                                    # code...
                                    $lpkDone++;
                                }
                                else{
                                    $lpkNDone++;
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
                    $subJob2[$key_sj]['listBlok'][$key_lt]['plotDone'] = $lpDone;
                    $subJob2[$key_sj]['listBlok'][$key_lt]['plotNDone'] = $lpNDone;
                    $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'plotNDone');
                    $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'plotDone');
                    $this->move_to_top($subJob2[$key_sj]['listBlok'][$key_lt], 'blok');
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
            unset($job2[$key_j]['jobCode']);
            unset($job2[$key_j]['Description']);
        }

        $user2[0]['RKM'] = $job2;

        return $user2;
    }

}
