<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class AppController extends Controller
{
    // public function index()
    // {
    // 	$arr 	    = array();

    //     ####################################GET ALL DATA###################################################
    //     $user2      = $this->removeWhitespace(DB::table('users')->select('id','name','username','password_decrypt','codePekerja')->get());

    //     $pekerja2   = $this->removeWhitespace(DB::table('EWS_PEKERJA')->get());
    	
    //     $role2      = $this->removeWhitespace(DB::table('EWS_ROLE_USER')->get());

    //     $mandor2    = $this->removeWhitespace(DB::table('EWS_MANDOR')->get());
        
    //     $m_pekerja2 = $this->removeWhitespace(DB::table('EWS_MANDOR_PEKERJA')->get());

    //     $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')->get());
    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         $date = date_create($rkm['rkhDate']);
    //         unset($rkm2[$key_rkm]['rkhDate']);
    //         # tanggal bulan tahun
    //         $rkm2[$key_rkm]['rkhDate'] = date_format($date, 'd F Y');
    //         $rkm2[$key_rkm]['rkhDate2'] = date_format($date, 'd-m-Y');
    //         # jam:menit:detik
    //         $rkm2[$key_rkm]['rkhTime'] = date_format($date, 'H:i:s');
    //     }

    //     $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());

    //     $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->get());
        
    //     $plant2     = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->get());
        
    //     $trans_mandor2     = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')->get());
    //     ####################################GET ALL DATA###################################################

    //     ####################################CREATE ARRAY USER###################################################
    //     # memasukkan nama tukang ke setiap code tukang
    //     foreach ($m_pekerja2 as $key_mp => $m_pekerja) {
    //         # code...
    //         foreach ($pekerja2 as $key_p => $pekerja) {
    //             # code...
    //             if ($pekerja['codePekerja'] == $m_pekerja['codePekerja']) {
    //                 # code...
    //                 $m_pekerja2[$key_mp]['namaTukang'] = $pekerja['namaPekerja'];
    //                 $m_pekerja2[$key_mp]['codePekerjaTukang'] = $pekerja['codePekerja'];
    //                 unset($m_pekerja2[$key_mp]['codePekerja']);
    //             }
    //         }
    //     }

    //     # memasukkan role [EWS_ROLE_USER(id)] ke dalam list pekerja [EWS_PEKERJA(idRole)]
    //     foreach ($pekerja2 as $key_p => $pekerja) {
    //         # code...
    //         foreach ($role2 as $key_r => $role) {
    //             # code...
    //             if ($pekerja['idRole'] == $role['id']) {
    //                 # code...
    //                 $pekerja2[$key_p]['detailRole'] = $role;
    //             }
    //         }
    //     }

    //     # memasukkan pekerja yang dibawahi mandor [EWS_MANDOR_PEKERJA(codeMandor)] ke dalam mandor [EWS_MANDOR(codeMandor)]
    //     # khusus role mandor
    //     foreach ($mandor2 as $key_m => $mandor) {
    //         # code...
    //         foreach ($m_pekerja2 as $key_mp => $m_pekerja) {
    //             # code...
    //             if ($m_pekerja['codeMandor'] == $mandor['codeMandor']) {
    //                 # code...
    //                 unset($m_pekerja['codeMandor']);
    //                 $mandor2[$key_m]['tukang'][] = $m_pekerja;
    //             }
    //         }
    //     }

    //     # memasukkan data mandor [EWS_MANDOR(codePekerja)] ke dalam data pekerja [EWS_PEKERJA(codePekerja)]
    //     foreach ($pekerja2 as $key_p => $pekerja) {
    //         # code...
    //         foreach ($mandor2 as $key_m => $mandor) {
    //             # code...
    //             if ($pekerja['codePekerja'] == $mandor['codePekerja']) {
    //                 # code...
    //                 $pekerja2[$key_p]['detailPekerja'] = $mandor;
    //             }
    //         }
    //     }

    //     # memasukkan data pekerja [EWS_PEKERJA(codePekerja)] ke dalam data user [users(codePekerja)]
    // 	foreach ($user2 as $key_u => $user) {

    // 		foreach ($pekerja2 as $key_p => $pekerja) {
    // 			# code...
    // 			if ($user['codePekerja'] == $pekerja['codePekerja']) {
    // 				# code...
    // 				$user2[$key_u]['identitasPekerja'] = $pekerja;
    // 			}
    // 		}
    // 	}

    //     # menghilangkan data-data redudansi dan tidak diperlukan
    //     foreach ($user2 as $key_u => $user) {
    //         # code...
    //         unset($user2[$key_u]['identitasPekerja']['codePekerja']);
    //         unset($user2[$key_u]['identitasPekerja']['detailPekerja']['codePekerja']);
    //         unset($user2[$key_u]['name']);
    //     }
    //     $arr['USER'] = $user2;
    //     ####################################CREATE ARRAY USER###################################################

    //     ####################################CREATE ARRAY RKM###################################################
    //     #------------------------------Memasukkan Job----------------------------------------#
    //     foreach ($job2 as $key_j => $job) {
    //         # code...
    //         foreach ($subJob2 as $key_sj => $subJob) {
    //             # code...
    //             if ($subJob['jobCode'] == $job['jobCode']) {
    //                 # code...
    //                 $job2[$key_j]['childJob'][] = $subJob;
    //             }
    //         }
    //     }

    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         foreach ($job2 as $key_j => $job) {
    //             # code...
    //             if (isset($job['childJob'])) {
    //                 # code...
    //                 foreach ($job['childJob'] as $key_cj => $childJob) {
    //                     # code...
    //                     if ($rkm['codeAlojob'] == $childJob['subJobCode']) {
    //                         # code...
    //                         $rkm2[$key_rkm]['parentJobCode'] = $job['jobCode'];
    //                         $rkm2[$key_rkm]['parentJobName'] = $job['Description'];
    //                         $rkm2[$key_rkm]['childJobCode'] = $childJob['subJobCode'];
    //                         $rkm2[$key_rkm]['childJobName'] = $childJob['Description'];
    //                         // unset($rkm2[$key_rkm]['codeAlojob']);
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     #------------------------------Memasukkan Job----------------------------------------#
    //     #------------------------------Memasukkan Lokasi Tanaman----------------------------------------#
    //     $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('codeBlok')->groupBy('codeBlok')->get());
    //     $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('plot')->groupBy('plot')->get());
    //     $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('baris')->groupBy('baris')->get());

    //     $listPlant  = array();
    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         foreach ($listBlok2 as $key_lbl => $listBlok) {
    //             # code...
    //             if ($rkm['codeBlok'] == $listBlok['codeBlok']) {
    //                 # code...
    //                 $rkm2[$key_rkm]['listTanaman'][]['codeBlok'] = $listBlok['codeBlok'];
    //                 // if (($rkm['barisStart'] == $plant['baris']) || ($rkm['barisEnd'] == $plant['baris'])) {
    //                 //     # code...
    //                 // }
    //             }
    //         }
    //     }

    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
    //             # code...
    //             $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'] = $listPlot2;
    //         }
    //     }

    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
    //             # code...
    //             foreach ($listTanaman['listPlot'] as $key_lp => $listPlot) {
    //                 # code...
    //                 foreach ($listBaris2 as $key_lbr => $listBaris) {
    //                     # code...
    //                     if ($listBaris['baris'] >= $rkm['barisStart']  && $listBaris['baris'] <= $rkm['barisEnd']) {
    //                         # code...
    //                         $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][] = $listBaris;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
    //             # code...
    //             foreach ($listTanaman['listPlot'] as $key_lp => $listPlot) {
    //                 # code...
    //                 foreach ($listPlot['listBaris'] as $key_lbr => $listBaris) {
    //                     # code...
    //                     foreach ($plant2 as $key_p => $plant) {
    //                         # code...
    //                         if (($plant['codeBlok'] == $listTanaman['codeBlok']) && 
    //                             ($plant['plot'] ==  $listPlot['plot']) && 
    //                             ($plant['baris'] == $listBaris['baris'])) {
    //                             # code...
    //                             $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lbr]['pokok'][] = $plant;
    //                             // $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lbr]['pokok'][]['status'] = 0;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     foreach ($rkm2 as $key_rkm => $rkm) {
    //         # code...
    //         foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
    //             # code...
    //             foreach ($listTanaman['listPlot'] as $key_lp => $listPlot) {
    //                 # code...
    //                 foreach ($listPlot['listBaris'] as $key_lbr => $listBaris) {
    //                     # code...
    //                     foreach ($listBaris['pokok'] as $key_p => $listPokok) {
    //                         # code...
    //                         $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lbr]['pokok'][$key_p]['status'] = 0;
    //                         // if (($plant['codeBlok'] == $listTanaman['codeBlok']) && 
    //                         //     ($plant['plot'] ==  $listPlot['plot']) && 
    //                         //     ($plant['baris'] == $listBaris['baris'])) {
    //                         //     # code...
    //                         // }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     // foreach ($rkm2 as $key_rkm => $rkm) {
    //     //     # code...
    //     //     foreach ($plant2 as $key_p => $plant) {
    //     //         # code...
    //     //         if ($rkm['codeBlok'] == $plant['codeBlok']) {
    //     //             # code...
    //     //             if (($rkm['barisStart'] == $plant['baris']) || ($rkm['barisEnd'] == $plant['baris'])) {
    //     //                 # code...
    //     //                 $rkm2[$key_rkm]['listTanaman'][] = $plant;
    //     //             }
    //     //         }
    //     //     }
    //     // }
    //     #------------------------------Memasukkan Lokasi Tanaman----------------------------------------#
    //     $arr['RKM'] = $rkm2;
    //     ####################################CREATE ARRAY RKM###################################################
        
    // 	return $arr;
    // }

    public function getUser (Request $request)
    {
        # code...
        $user2 = $this->removeWhitespace(DB::table('users')
            ->select('id','name','username','password_decrypt','codePekerja')
            ->where('username', $request->username)
            ->where('password_decrypt', $request->password)
            ->get());

        // $arr        = array();

        ####################################GET ALL DATA###################################################
        // $user2      = $this->removeWhitespace(DB::table('users')->select('id','name','username','password_decrypt','codePekerja')->get());

        $pekerja2   = $this->removeWhitespace(DB::table('EWS_PEKERJA')->get());
        
        $role2      = $this->removeWhitespace(DB::table('EWS_ROLE_USER')->get());

        $mandor2    = $this->removeWhitespace(DB::table('EWS_MANDOR')->get());
        
        $m_pekerja2 = $this->removeWhitespace(DB::table('EWS_MANDOR_PEKERJA')->get());
        ####################################GET ALL DATA###################################################

        ####################################CREATE ARRAY USER###################################################
        # memasukkan nama tukang ke setiap code tukang
        foreach ($m_pekerja2 as $key_mp => $m_pekerja) {
            # code...
            foreach ($pekerja2 as $key_p => $pekerja) {
                # code...
                if ($pekerja['codePekerja'] == $m_pekerja['codePekerja']) {
                    # code...
                    $m_pekerja2[$key_mp]['namaTukang'] = $pekerja['namaPekerja'];
                    $m_pekerja2[$key_mp]['codePekerjaTukang'] = $pekerja['codePekerja'];
                    unset($m_pekerja2[$key_mp]['codePekerja']);

                    $m_pekerja2[$key_mp]['idTukang'] = $m_pekerja2[$key_mp]['id'];
                    unset($m_pekerja2[$key_mp]['id']);
                    
                    $this->move_to_top($m_pekerja2[$key_mp], 'idTukang');
                }
            }   
        }

        # memasukkan role [EWS_ROLE_USER(id)] ke dalam list pekerja [EWS_PEKERJA(idRole)]
        foreach ($pekerja2 as $key_p => $pekerja) {
            # code...
            foreach ($role2 as $key_r => $role) {
                # code...
                if ($pekerja['idRole'] == $role['id']) {
                    # code...
                    $pekerja2[$key_p]['detailRole'] = $role;
                }
            }
        }

        # memasukkan pekerja yang dibawahi mandor [EWS_MANDOR_PEKERJA(codeMandor)] ke dalam mandor [EWS_MANDOR(codeMandor)]
        # khusus role mandor
        foreach ($mandor2 as $key_m => $mandor) {
            # code...
            foreach ($m_pekerja2 as $key_mp => $m_pekerja) {
                # code...
                if ($m_pekerja['codeMandor'] == $mandor['codeMandor']) {
                    # code...
                    unset($m_pekerja['codeMandor']);
                    $mandor2[$key_m]['tukang'][] = $m_pekerja;
                }
            }
        }

        # memasukkan data mandor [EWS_MANDOR(codePekerja)] ke dalam data pekerja [EWS_PEKERJA(codePekerja)]
        foreach ($pekerja2 as $key_p => $pekerja) {
            # code...
            foreach ($mandor2 as $key_m => $mandor) {
                # code...
                if ($pekerja['codePekerja'] == $mandor['codePekerja']) {
                    # code...
                    $pekerja2[$key_p]['detailPekerja'] = $mandor;
                }
            }
        }

        # memasukkan data pekerja [EWS_PEKERJA(codePekerja)] ke dalam data user [users(codePekerja)]
        foreach ($user2 as $key_u => $user) {

            foreach ($pekerja2 as $key_p => $pekerja) {
                # code...
                if ($user['codePekerja'] == $pekerja['codePekerja']) {
                    # code...
                    $user2[$key_u]['identitasPekerja'] = $pekerja;
                }
            }
        }

        # menghilangkan data-data redudansi dan tidak diperlukan
        foreach ($user2 as $key_u => $user) {
            # code...
            unset($user2[$key_u]['identitasPekerja']['codePekerja']);
            unset($user2[$key_u]['identitasPekerja']['detailPekerja']['codePekerja']);
            unset($user2[$key_u]['name']);

            $user2[$key_u]['idUser'] = $user2[$key_u]['id'];
            unset($user2[$key_u]['id']);
            unset($user2[$key_u]['identitasPekerja']['idRole']);

            $user2[$key_u]['identitasPekerja']['detailRole']['idDetailRole'] = $user2[$key_u]['identitasPekerja']['detailRole']['id'];
            unset($user2[$key_u]['identitasPekerja']['detailRole']['id']);

            $this->move_to_top($user2[$key_u], 'idUser');
            $this->move_to_top($user2[$key_u]['identitasPekerja']['detailRole'], 'idDetailRole');
        }
        // $arr['USER'] = $user2;
        ####################################CREATE ARRAY USER###################################################

        ####################################INSERT WORKER PH###################################################
        $ph_pekerja2 = $this->removeWhitespace(DB::table('EWS_PH_PEKERJA')->get());
        foreach ($ph_pekerja2 as $key_php => $ph_pekerja) {
            # code...
            foreach ($pekerja2 as $key_p => $pekerja) {
                # code...
                if ($pekerja['codePekerja'] == $ph_pekerja['codePekerjaTukang']) {
                    # code...
                    $ph_pekerja2[$key_php]['namaTukang'] = $pekerja['namaPekerja'];
                    // $ph_pekerja2[$key_php]['codePekerjaTukang'] = $pekerja['codePekerja'];
                    // unset($ph_pekerja2[$key_php]['codePekerja']);

                    $ph_pekerja2[$key_php]['idTukang'] = $ph_pekerja2[$key_php]['id'];
                    unset($ph_pekerja2[$key_php]['id']);
                    
                    $this->move_to_top($ph_pekerja2[$key_php], 'idTukang');
                }
            }   
        }

        foreach ($user2 as $key_u => $user) {
            # code...
            foreach ($ph_pekerja2 as $key_php => $ph_pekerja) {
                # code...
                if ($user['identitasPekerja']['detailRole']['idDetailRole'] == 4 || $user['identitasPekerja']['detailRole']['idDetailRole']== 5) {
                    # code...
                    if ($user['codePekerja'] == $ph_pekerja['codePekerjaPH']) {
                        # code...
                        unset($ph_pekerja['codePekerjaPH']);
                        $user2[$key_u]['identitasPekerja']['detailPekerja']['tukang'][] = $ph_pekerja;
                    }
                }
            }
        }
        ####################################INSERT WORKER PH###################################################

        $date = '07-02-2019';
        // $date = $request->date;
        $rkm = $this->getRKMMandor($user2[0]['idUser'], $date);
        $user2[0]['RKM'] = $rkm;
        return json_encode($user2, JSON_PRETTY_PRINT);
    }

    public function getAllUser ()
    {
        $arr        = array();

        ####################################GET ALL DATA###################################################
        $user2      = $this->removeWhitespace(DB::table('users')->select('id','name','username','password_decrypt','codePekerja')->get());

        $pekerja2   = $this->removeWhitespace(DB::table('EWS_PEKERJA')->get());
        
        $role2      = $this->removeWhitespace(DB::table('EWS_ROLE_USER')->get());

        $mandor2    = $this->removeWhitespace(DB::table('EWS_MANDOR')->get());
        
        $m_pekerja2 = $this->removeWhitespace(DB::table('EWS_MANDOR_PEKERJA')->get());
        ####################################GET ALL DATA###################################################

        ####################################CREATE ARRAY USER###################################################
        # memasukkan nama tukang ke setiap code tukang
        foreach ($m_pekerja2 as $key_mp => $m_pekerja) {
            # code...
            foreach ($pekerja2 as $key_p => $pekerja) {
                # code...
                if ($pekerja['codePekerja'] == $m_pekerja['codePekerja']) {
                    # code...
                    $m_pekerja2[$key_mp]['namaTukang'] = $pekerja['namaPekerja'];
                    $m_pekerja2[$key_mp]['codePekerjaTukang'] = $pekerja['codePekerja'];
                    unset($m_pekerja2[$key_mp]['codePekerja']);

                    $m_pekerja2[$key_mp]['idTukang'] = $m_pekerja2[$key_mp]['id'];
                    unset($m_pekerja2[$key_mp]['id']);
                    
                    $this->move_to_top($m_pekerja2[$key_mp], 'idTukang');
                }
            }   
        }

        # memasukkan role [EWS_ROLE_USER(id)] ke dalam list pekerja [EWS_PEKERJA(idRole)]
        foreach ($pekerja2 as $key_p => $pekerja) {
            # code...
            foreach ($role2 as $key_r => $role) {
                # code...
                if ($pekerja['idRole'] == $role['id']) {
                    # code...
                    $pekerja2[$key_p]['detailRole'] = $role;
                }
            }
        }

        # memasukkan pekerja yang dibawahi mandor [EWS_MANDOR_PEKERJA(codeMandor)] ke dalam mandor [EWS_MANDOR(codeMandor)]
        # khusus role mandor
        foreach ($mandor2 as $key_m => $mandor) {
            # code...
            foreach ($m_pekerja2 as $key_mp => $m_pekerja) {
                # code...
                if ($m_pekerja['codeMandor'] == $mandor['codeMandor']) {
                    # code...
                    unset($m_pekerja['codeMandor']);
                    $mandor2[$key_m]['tukang'][] = $m_pekerja;
                }
            }
        }

        # memasukkan data mandor [EWS_MANDOR(codePekerja)] ke dalam data pekerja [EWS_PEKERJA(codePekerja)]
        foreach ($pekerja2 as $key_p => $pekerja) {
            # code...
            foreach ($mandor2 as $key_m => $mandor) {
                # code...
                if ($pekerja['codePekerja'] == $mandor['codePekerja']) {
                    # code...
                    $pekerja2[$key_p]['detailPekerja'] = $mandor;
                }
            }
        }

        # memasukkan data pekerja [EWS_PEKERJA(codePekerja)] ke dalam data user [users(codePekerja)]
        foreach ($user2 as $key_u => $user) {

            foreach ($pekerja2 as $key_p => $pekerja) {
                # code...
                if ($user['codePekerja'] == $pekerja['codePekerja']) {
                    # code...
                    $user2[$key_u]['identitasPekerja'] = $pekerja;
                }
            }
        }

        # menghilangkan data-data redudansi dan tidak diperlukan
        foreach ($user2 as $key_u => $user) {
            # code...
            unset($user2[$key_u]['identitasPekerja']['codePekerja']);
            unset($user2[$key_u]['identitasPekerja']['detailPekerja']['codePekerja']);
            unset($user2[$key_u]['name']);

            $user2[$key_u]['idUser'] = $user2[$key_u]['id'];
            unset($user2[$key_u]['id']);
            unset($user2[$key_u]['identitasPekerja']['idRole']);

            $user2[$key_u]['identitasPekerja']['detailRole']['idDetailRole'] = $user2[$key_u]['identitasPekerja']['detailRole']['id'];
            unset($user2[$key_u]['identitasPekerja']['detailRole']['id']);

            $this->move_to_top($user2[$key_u], 'idUser');
            $this->move_to_top($user2[$key_u]['identitasPekerja']['detailRole'], 'idDetailRole');
        }
        $arr['USER'] = $user2;
        ####################################CREATE ARRAY USER###################################################

        ####################################INSERT WORKER PH###################################################
        $ph_pekerja2 = $this->removeWhitespace(DB::table('EWS_PH_PEKERJA')->get());
        foreach ($ph_pekerja2 as $key_php => $ph_pekerja) {
            # code...
            foreach ($pekerja2 as $key_p => $pekerja) {
                # code...
                if ($pekerja['codePekerja'] == $ph_pekerja['codePekerjaTukang']) {
                    # code...
                    $ph_pekerja2[$key_php]['namaTukang'] = $pekerja['namaPekerja'];
                    // $ph_pekerja2[$key_php]['codePekerjaTukang'] = $pekerja['codePekerja'];
                    // unset($ph_pekerja2[$key_php]['codePekerja']);

                    $ph_pekerja2[$key_php]['idTukang'] = $ph_pekerja2[$key_php]['id'];
                    unset($ph_pekerja2[$key_php]['id']);
                    
                    $this->move_to_top($ph_pekerja2[$key_php], 'idTukang');
                }
            }   
        }

        foreach ($user2 as $key_u => $user) {
            # code...
            foreach ($ph_pekerja2 as $key_php => $ph_pekerja) {
                # code...
                if ($user['identitasPekerja']['detailRole']['idDetailRole'] == 4 || $user['identitasPekerja']['detailRole']['idDetailRole']== 5) {
                    # code...
                    if ($user['codePekerja'] == $ph_pekerja['codePekerjaPH']) {
                        # code...
                        unset($ph_pekerja['codePekerjaPH']);
                        $user2[$key_u]['identitasPekerja']['detailPekerja']['tukang'][] = $ph_pekerja;
                    }
                }
            }
        }
        ####################################INSERT WORKER PH###################################################


        return $user2;
        // return $this->index()['USER'];
    }

    public function getRKMMandor ($id_user, $date)
    {
        $tgl = date_create($date);
        $tgl_ubah = date_format($tgl, 'Y-m-d');
        ####################################GET ALL DATA###################################################
        $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')->whereBetween('rkhDate', [$tgl_ubah, $tgl_ubah.' 23:59:59.999'])->get());
        // $rkm2       = $this->removeWhitespace(DB::table('EWS_JADWAL_RKM')->get());
        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            $date = date_create($rkm['rkhDate']);
            unset($rkm2[$key_rkm]['rkhDate']);
            # tanggal bulan tahun
            $rkm2[$key_rkm]['rkhDate'] = date_format($date, 'd F Y');
            # jam:menit:detik
            $rkm2[$key_rkm]['rkhTime'] = date_format($date, 'H:i:s');
        }
        $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());

        $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->get());
        
        $plant2     = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->get());
        ####################################GET ALL DATA###################################################

        ####################################CREATE ARRAY RKM###################################################
        #------------------------------Memasukkan Job----------------------------------------#
        foreach ($job2 as $key_j => $job) {
            # code...
            foreach ($subJob2 as $key_sj => $subJob) {
                # code...
                if ($subJob['jobCode'] == $job['jobCode']) {
                    # code...
                    $job2[$key_j]['childJob'][] = $subJob;
                }
            }
        }

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($job2 as $key_j => $job) {
                # code...
                if (isset($job['childJob'])) {
                    # code...
                    foreach ($job['childJob'] as $key_cj => $childJob) {
                        # code...
                        if ($rkm['codeAlojob'] == $childJob['subJobCode']) {
                            # code...
                            $rkm2[$key_rkm]['parentJobCode'] = $job['jobCode'];
                            $rkm2[$key_rkm]['parentJobName'] = $job['Description'];
                            $rkm2[$key_rkm]['childJobCode'] = $childJob['subJobCode'];
                            $rkm2[$key_rkm]['childJobName'] = $childJob['Description'];
                        }
                    }
                }
            }
        }
        #------------------------------Memasukkan Job----------------------------------------#
        #------------------------------Memasukkan Lokasi Tanaman----------------------------------------#
        $listBlok2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('codeBlok')->groupBy('codeBlok')->get());
        $listPlot2  = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('plot')->groupBy('plot')->get());
        $listBaris2 = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('baris')->groupBy('baris')->get());

        $listPlant  = array();
        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($listBlok2 as $key_lbl => $listBlok) {
                # code...
                if ($rkm['codeBlok'] == $listBlok['codeBlok']) {
                    # code...
                    $rkm2[$key_rkm]['listTanaman'][]['codeBlok'] = $listBlok['codeBlok'];
                }
            }
        }

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
                # code...
                $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'] = $listPlot2;
            }
        }

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
                # code...
                foreach ($listTanaman['listPlot'] as $key_lp => $listPlot) {
                    # code...
                    foreach ($listBaris2 as $key_lbr => $listBaris) {
                        # code...
                        if ($listBaris['baris'] >= $rkm['barisStart']  && $listBaris['baris'] <= $rkm['barisEnd']) {
                            # code...
                            $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][] = $listBaris;
                        }
                    }
                }
            }
        }

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
                # code...
                foreach ($listTanaman['listPlot'] as $key_lp => $listPlot) {
                    # code...
                    foreach ($listPlot['listBaris'] as $key_lbr => $listBaris) {
                        # code...
                        foreach ($plant2 as $key_p => $plant) {
                            # code...
                            if (($plant['codeBlok'] == $listTanaman['codeBlok']) && 
                                ($plant['plot'] ==  $listPlot['plot']) && 
                                ($plant['baris'] == $listBaris['baris'])) {
                                # code...
                                $plant['jmlMinggu'] = $this->datediff('ww', $plant['PlantingDate'], now(), FALSE);
                                $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lbr]['pokok'][] = $plant;
                            }
                        }
                    }
                }
            }
        }

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($rkm['listTanaman'] as $key_lt => $listTanaman) {
                # code...
                foreach ($listTanaman['listPlot'] as $key_lp => $listPlot) {
                    # code...
                    foreach ($listPlot['listBaris'] as $key_lbr => $listBaris) {
                        # code...
                        foreach ($listBaris['pokok'] as $key_p => $listPokok) {
                            # code...
                            $rkm2[$key_rkm]['listTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lbr]['pokok'][$key_p]['status'] = 0;
                        }
                    }
                }
            }
        }

        #------------------------------Memasukkan Lokasi Tanaman----------------------------------------#
        ####################################CREATE ARRAY RKM###################################################
        
        $user2 = $this->getAllUser();

        $rkm_hasil = array();

        foreach ($rkm2 as $key_rkm => $rkm) {
            # code...
            foreach ($user2 as $key_user => $user) {
                # code...
                if ($id_user == $user['idUser']) {
                    # code...
                    if ($rkm['mandorCode'] == $user['identitasPekerja']['detailPekerja']['codeMandor']) {
                        # code...
                        $rkm_hasil[] = $rkm;
                    }
                }
            }
        }

        return $rkm_hasil;
    }

    public function getRKMKawil($id_user)
    {
        # get all list job available for kawil from mandor transaction
        $listJob2   = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')->distinct()->select('subJobCode', 'userid')->get());

        $job2       = $this->removeWhitespace(DB::table('EWS_JOB')->get());
        $subJob2    = $this->removeWhitespace(DB::table('EWS_SUB_JOB')->get());

        foreach ($job2 as $key_j => $job) {
            # code...
            foreach ($subJob2 as $key_sj => $subJob) {
                # code...
                if ($subJob['jobCode'] == $job['jobCode']) {
                    # code...
                    $job2[$key_j]['childJob'][] = $subJob;
                }
            }
        }

        foreach ($listJob2 as $key_lj => $listJob) {
            # code...
            foreach ($job2 as $key_j => $job) {
                # code...
                if (isset($job['childJob'])) {
                    # code...
                    foreach ($job['childJob'] as $key_cj => $childJob) {
                        # code...
                        if ($listJob['subJobCode'] == $childJob['subJobCode']) {
                            # code...
                            $listJob2[$key_lj]['parentJobCode'] = $job['jobCode'];
                            $listJob2[$key_lj]['parentJobName'] = $job['Description'];
                            $listJob2[$key_lj]['childJobCode'] = $childJob['subJobCode'];
                            $listJob2[$key_lj]['childJobName'] = $childJob['Description'];
                            unset($listJob2[$key_lj]['subJobCode']);
                        }
                    }
                }
            }
        }

        # retrieve all user identity by userid in listjob
        foreach ($listJob2 as $key_lj => $listJob) {
            # code...
            $id_mdr = DB::table('users')->select('id','codePekerja')->where('id', '=', $listJob['userid'])->first();
            $cd_mdr = DB::table('EWS_MANDOR')->where('codePekerja', '=', $id_mdr->codePekerja)->value('codeMandor');
            $nm_mdr = DB::table('EWS_PEKERJA')->where('codePekerja', '=', $id_mdr->codePekerja)->value('namaPekerja');

            $listJob2[$key_lj]['identitasMandor']['userid'] = rtrim($id_mdr->id);
            $listJob2[$key_lj]['identitasMandor']['codePekerja'] = rtrim($id_mdr->codePekerja);
            $listJob2[$key_lj]['identitasMandor']['codeMandor'] = rtrim($cd_mdr);
            $listJob2[$key_lj]['identitasMandor']['namaMandor'] = rtrim($nm_mdr);

            unset($listJob2[$key_lj]['userid']);
        }

        # generate location plant
        $cd_blok            = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->distinct()->select('codeBlok')->get());
        $cd_plot            = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->distinct()->select('plot')->get());
        $cd_bris            = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->distinct()->select('baris')->get());
        $lok_tanam = array();
        $lok_tanam = $cd_blok;

        foreach ($lok_tanam as $key_lt => $lok_t) {
            # code...
            $lok_tanam[$key_lt]['listPlot'] = $cd_plot;
        }

        foreach ($lok_tanam as $key_lt => $lok_t) {
            # code...
            foreach ($lok_t['listPlot'] as $key_lp => $list_p) {
                # code...
                $lok_tanam[$key_lt]['listPlot'][$key_lp]['listBaris'] = $cd_bris;
            }
        }

        # inserting location plant into list job
        foreach ($listJob2 as $key_lj => $listJob) {
            # code...
            foreach ($lok_tanam as $key_lt => $lok_t) {
                # code...
                $listJob2[$key_lj]['detailTanaman'][] = $lok_t;
            }
        }

        # inserting mandor trans into list job
        # get all mandor transaction for purpose of kawil process by finding work done by mandor
        $plant_date2        = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->select('codeTanaman', 'PlantingDate')->get());
        $trans_mandor2      = $this->removeWhitespace(DB::table('EWS_TRANS_MANDOR')->get());
        foreach ($trans_mandor2 as $key_tm => $trans_mandor) {
            # code...
            $date = date_create($trans_mandor['created_at']);
            # tanggal bulan tahun
            $trans_mandor2[$key_tm]['tanggalKerja'] = date_format($date, 'd F Y');
            # jam:menit:detik
            $trans_mandor2[$key_tm]['waktuKerja'] = date_format($date, 'H:i:s');
            unset($trans_mandor2[$key_tm]['created_at']);


            foreach ($plant_date2 as $key_pd => $plant_date) {
                # code...
                if ($plant_date['codeTanaman'] == $trans_mandor['codeTanaman']) {
                    # code...
                    $trans_mandor2[$key_tm]['jmlMinggu'] = $this->datediff('ww', $plant_date['PlantingDate'], now(), FALSE);
                    $trans_mandor2[$key_tm]['status'] = 0;
                }
            }
        }

        foreach ($listJob2 as $key_lj => $listJob) {
            # code...
            foreach ($listJob['detailTanaman'] as $key_lt => $detailTanaman) {
                # code...
                foreach ($detailTanaman['listPlot'] as $key_lp => $listPlot) {
                    # code...
                    foreach ($listPlot['listBaris'] as $key_lb => $listBaris) {
                        # code...
                        foreach ($trans_mandor2 as $key_tm => $trans_mandor) {
                            # code...
                            $cd_tanam = preg_split('/[ .,\/]/', preg_replace('/.[A-R]0./', '.', $trans_mandor['codeTanaman']));
                            if (
                                ($trans_mandor['subJobCode'] == $listJob['childJobCode']) &&
                                ($detailTanaman['codeBlok'] == $cd_tanam[0]) && 
                                ($listPlot['plot'] == $cd_tanam[1]) && 
                                ($listBaris['baris'] == $cd_tanam[2])
                            ) 
                            {
                                $listJob2[$key_lj]['detailTanaman'][$key_lt]['listPlot'][$key_lp]['listBaris'][$key_lb]['pokok'][] = $trans_mandor;
                            }
                        }
                    }
                }
            }
        }
        return $listJob2;
    }

    public function getAllPokok()
    {
        # code...
        $pokok2      = $this->removeWhitespace(DB::table('EWS_LOK_TANAMAN')->get());
        return $pokok2;
    }

    public function storeMandor (Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
            # 3 MACAM INPUT, 
            # 1. PLANTCARE, MODEL PEMUPUKAN
            # KHUSUS PEMUPUKAN BENTUK YANG DIBERIKAN BERUPA POKOK DARI NOMOR X KE NOMOR Y
            # JADI API GENERATE 
            // foreach ($request as $key_req => $requ) {
            //     # code...
            //     if ($requ['subJobCode'] == '') {
            //         # code...
            //     }
            // }
            # 2. FRUIT CARE, MODEL BI [NORMAL]
                # code...
                DB::table('EWS_TRANS_MANDOR')->insert([
                    'subJobCode' => $req->subJobCode,
                    'userid' => $req->userid,
                    'codeTukang' => $req->codeTukang,
                    'codeTanaman' => $req->codeTanaman,
                    'mandorNote' => $req->mandorNote,
                    'totalHand' => $req->totalHand,
                    'totalFinger' => $req->totalFinger,
                    'totalLeaf' => $req->totalLeaf,
                    'ribbonColor' => $req->ribbonColor,
                    'skimmingSize' => $req->skimmingSize,
                    'create_at' => $create_at
                ]);
        }
        // $db = DB::table('EWS_TRANS_MANDOR')->insert([
        //     'subJobCode' => $request->subJobCode,
        //     'userid' => $request->userid,
        //     'codeTukang' => $request->codeTukang,
        //     'codeTanaman' => $request->codeTanaman,
        //     'mandorNote' => $request->mandorNote,
        //     'totalHand' => $request->totalHand,
        //     'totalFinger' => $request->totalFinger,
        //     'totalLeaf' => $request->totalLeaf,
        //     'ribbonColor' => $request->ribbonColor,
        //     'skimmingSize' => $request->skimmingSize,
        //     'create_at' => $create_at
        // ]);
        # 3. FRUIT CARE, MODEL MARKING
        # 4. PANEN, MODEL SKIMMING 

        // return response()->json($response, status, 201);
        return $request;
    }

    public function storeKawil (Request $request)
    {
        $date = date_create($request->tanggal.' '.$request->waktu);
        # tanggal bulan tahun
        $create_at = date_format($date, 'Y-m-d H:i:s.B');

        foreach ($request as $key_req => $req) {
                DB::table('EWS_TRANS_KAWIL')->insert([
                    'idEWSTransMandor' => $req->idEWSTransMandor,
                    'subJobCode' => $req->subJobCode,
                    'kawilNote' => $req->kawilNote,
                    'userid' => $req->userid,
                    'create_at' => $create_at
                ]);
        }

        return $request;
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
}
