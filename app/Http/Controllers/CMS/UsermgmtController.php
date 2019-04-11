<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsermgmtController extends CMSController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('EWS_VW_DETAIL_USER')
            // $query = DB::table('users')
            // ->join('EWS_PEKERJA', 'users.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
            // ->join('EWS_ROLE_USER', 'EWS_PEKERJA.idRole', '=', 'EWS_ROLE_USER.id')
            // ->select('users.id', 'users.username', 'users.email', 'EWS_PEKERJA.codePekerja', 'EWS_PEKERJA.namaPekerja', 'EWS_ROLE_USER.namaRole', 'EWS_ROLE_USER.descRole')
            ->get();
            $users = $this->removeWhitespace($query);
            return DataTables::of($users)
                ->addColumn('aksi', function ($users)
                {
                    $onClick = "event.preventDefault();document.getElementById('destroy-form_".$users["id"]."').submit();";

                    return "
                    <div class='btn-group' role='group' aria-label='Button group with nested dropdown' id='btn-group-aksi'>
                        <button type='button' class='btn btn-secondary btn-success' id='showDetail' data-id=".$users["id"].">Show</button>
                        <div class='btn-group' role='group'>
                            <button id='btnGroupDrop1' type='button' class='btn btn-secondary dropdown-toggle btn-info' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                            Ubah
                            </button>
                            <div class='dropdown-menu' aria-labelledby='btnGroupDrop1'>
                                <a href=".route("usermgmt.edit", ["id" => $users["id"]."_detail"])." id='edit' class='btn dropdown-item' tabindex='-1' role='button' aria-disabled='false'>Detail</a> 

                                <a href=".route("usermgmt.edit", ["id" => $users["id"]."_password"])." id='edit' class='btn dropdown-item' tabindex='-1' role='button' aria-disabled='false'>Password</a> 
                            </div>
                        </div>
                        <button type='button' class='btn btn-secondary btn-warning' onclick=".$onClick.">Hapus</button>
                    </div>
                    <form id='destroy-form_".$users["id"]."' action=".route("usermgmt.destroy", ["id" => $users["id"]])." method='POST' style='display: none;'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='_token' value=".csrf_token().">
                    </form>
                    ";
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('cms.usermgmt.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['pekerja'] = $this->removeWhitespace(DB::table('EWS_PEKERJA')->orderBy('namaPekerja', 'asc')->get());
        $data['role'] = $this->removeWhitespace(DB::table('EWS_ROLE_USER')->orderBy('id', 'asc')->get());
        return view('cms.usermgmt.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => 'Kotak :attribute harus diisi/dipilih || ',
            'email' => 'Alamat email harus benar || ',
            'integer' => 'Kotak :attribute harus diisi/dipilih || ',
        ];
        
        if ($request->role == 8) {
            # code...
            $rules = [
                'username' => 'required|max:255|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'pekerja' => 'required|integer|unique:users,codePekerja',
                'role' => 'required|integer',
                'password' => 'required|string|min:6|confirmed'
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages)->validate();
        }else{
            # code...
            $rules = [
                'username' => 'required|max:255|unique:users',
                'email' => 'required|email|max:255|unique:users',
                'role' => 'required|integer',
                'password' => 'required|string|min:6|confirmed'
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages)->validate();

            if ($request->role == 1) {
                # code...
                $request->pekerja = 1;
            }

            if ($request->role == 2) {
                # code...
                $request->pekerja = 2;
            }
        }

        
        try {
            DB::table('users')->insert([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'password_decrypt' => $request->password,
                'codePekerja' => $request->pekerja,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->pekerja != NULL) {
                # code...
                DB::table('EWS_PEKERJA')
                    ->where('codePekerja', '=', $request->pekerja)
                    ->update(['idRole' => $request->role]);
            }
        } catch (Exception $e) {
            return $e;
        }

        return view('cms.usermgmt.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = DB::table('EWS_VW_DETAIL_USER') 
            ->where('id', '=', $id)
            ->first();
        $user = $this->removeWhitespace2($query);
        return "
            <div class='modal-header'>
                <h5 class='modal-title' id='showDetailLabel'>User Detail</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <div class='container'>
                    <div class='row'>
                        <div class='col-sm'>
                            Nama Akun
                        </div>
                        <div class='col-sm'>
                            ".$user['name']."
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm'>
                            Username
                        </div>
                        <div class='col-sm'>
                            ".$user['username']."
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm'>
                            Email
                        </div>
                        <div class='col-sm'>
                            ".$user['email']."
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm'>
                            Password
                        </div>
                        <div class='col-sm'>
                            ".$user['password_decrypt']."
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm'>
                            Kode Pekerja
                        </div>
                        <div class='col-sm'>
                            ".$user['codePekerja']."
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm'>
                            Nama Pekerja
                        </div>
                        <div class='col-sm'>
                            ".$user['namaPekerja']."
                        </div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
            </div>
        ";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $param = explode('_', $id);

        $data['id'] = $param[0];
        $data['status'] = $param[1];
        $data['user'] = $this->removeWhitespace2(DB::table('EWS_VW_DETAIL_USER')
            ->where('id', '=', $param[0])
            ->first());
        $data['role'] = $this->removeWhitespace(DB::table('EWS_ROLE_USER')->orderBy('id', 'asc')->get());
        $data['pekerja'] = $this->removeWhitespace(DB::table('EWS_VW_DETAIL_MANDOR')
                ->orderBy('namaPekerja', 'asc')
                ->get());
        return view('cms.usermgmt.edit', $data);
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
        $request->pekerja = $request->pekerja == 0 ? 'NULL' : $request->pekerja;
        
        $messages = [
            'required' => 'Kotak :attribute harus diisi/dipilih || ',
            'email' => 'Alamat email harus benar || ',
            'integer' => 'Kotak :attribute harus diisi/dipilih || ',
        ];

        if ($request->status == 'detail') {
            # code...
            $rules = [
                'name' => 'required|max:255',
                'username' => 'required|max:255',
                'email' => 'required|email|max:255',
                'pekerja' => 'integer',
                'role' => 'integer',
            ];

            if ($request->role == 1) {
                # code...
                $request->pekerja = 1;
            }

            if ($request->role == 2) {
                # code...
                $request->pekerja = 2;
            }
            
            $array = array(
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'codePekerja' => $request->pekerja,
                'updated_at' => now(),
            );
        }

        if ($request->status == 'password') {
            # code...
            $rules = [
                'old_password' => 'required|string|min:6',
                'new_password' => 'required|string|min:6|confirmed'
            ];
            
            $array = array(
                'password' => bcrypt($request->new_password),
                'password_decrypt' => $request->new_password,
                'updated_at' => now(),
            );

            $hashedPassword = DB::table('users')->where('id', '=', $id)->value('password');
            // return $hashedPassword;
            if (!Hash::check($request->old_password, $hashedPassword)) {
                // The passwords not match...
                return back()->with('password', 'Password lama salah');
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages)->validate();

        try {

            DB::table('users')
                ->where('id', '=', $id)
                ->update($array);

            if ($request->status == 'detail' && $request->pekerja != NULL) {
                # code...
                DB::table('EWS_PEKERJA')
                    ->where('codePekerja', '=', $request->pekerja)
                    ->update(['idRole' => $request->role]);
            }

        } catch (Exception $e) {
            return $e;            
        }

        return view('cms.usermgmt.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('users')->where('id', '=', $id)->delete();
        return redirect()->back()->with('alert', 'User Deleted!');
    }

    public function postRoleDropdown(Request $request)
    {
        # code...
        // return $request;
        if ($request->id == 8) { # mandor
            # code...
            $data = DB::table('EWS_VW_DETAIL_MANDOR')
                ->orderBy('namaPekerja', 'asc')
                ->get();
            $listData = $this->removeWhitespace($data);
            $return = '<option value="">Pilih Kode Pekerja...</option>';
            foreach($listData as $temp) 
                $return .= "<option value=".$temp['codePekerja'].">".$temp['namaPekerja']." [".$temp['codeMandor']."]</option>";
            return $return;
        }

        if ($request->id == 7) { # mandor
            # code...
            $data = DB::table('EWS_PEKERJA')
                ->orderBy('namaPekerja', 'asc')
                ->get();
            $listData = $this->removeWhitespace($data);
            $return = '<option value="">Pilih Kode Pekerja...</option>';
            foreach($listData as $temp) 
                if ($temp['idRole'] != NULL) {
                    # code...
                    $return .= "<option value=".$temp['codePekerja']." disabled>".$temp['namaPekerja']."</option>";
                }else{
                    $return .= "<option value=".$temp['codePekerja'].">".$temp['namaPekerja']."</option>";
                }
            return $return;
        }
    }
}
