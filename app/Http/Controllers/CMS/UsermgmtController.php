<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use App\User;
use App\Roles;
use App\Pekerja;
use App\Permission;
use App\Authorizable;
use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsermgmtController extends CMSController
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::select(['id', 'name', 'username', 'created_at'])->with('roles');
            
            return DataTables::of($users)
                ->addColumn('role', function ($users) {
                    if (isset($users->roles->first()->name)) {
                        return ucfirst($users->roles->implode('name', ', '));
                    }
                })
                ->addColumn('aksi', function ($users)
                {
                    $user = Auth::user();
                    $userid = Auth::id();

                    if ($user->hasPermissionTo('view_usermgmt')) {
                        $view = "<button type='button' class='btn btn-secondary btn-success' id='showDetail' data-id=".$users["id"].">Show</button>";
                    } else { $view = ""; }
                    if ($user->hasPermissionTo('edit_usermgmt')) {
                        $edit = "
                            <div class='btn-group' role='group'>
                                <button id='btnGroupDrop1' type='button' class='btn btn-secondary dropdown-toggle btn-info' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                Ubah
                                </button>
                                <div class='dropdown-menu' aria-labelledby='btnGroupDrop1'>
                                    <a href=".route("usermgmt.edit", ["id" => $users["id"]."_detail"])." id='edit' class='btn dropdown-item' tabindex='-1' role='button' aria-disabled='false'>Detail</a> 

                                    <a href=".route("usermgmt.edit", ["id" => $users["id"]."_password"])." id='edit' class='btn dropdown-item' tabindex='-1' role='button' aria-disabled='false'>Password</a> 
                                </div>
                            </div>
                            ";
                    } else { $edit = ""; }
                    if ($user->hasPermissionTo('delete_usermgmt')) {
                        $delete = "<button type='button' class='btn btn-secondary btn-danger' id='penghapusan' data-toggle='modal' data-target='#hapusModal' data-url=".route("usermgmt.destroy", ["id" => $users["id"]]).">Hapus</button>";
                    } else { $delete = ""; }

                    return "
                    <div class='btn-group' role='group' aria-label='Button group with nested dropdown' id='btn-group-aksi'>
                        ".$view."
                        ".$edit."
                        ".$delete."
                    </div>
                    ";
                })
                ->addColumn('created', function ($users)
                {
                    return $users->created_at->toFormattedDateString();
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
        $data['pekerja'] = Pekerja::all()->sortBy('namaPekerja')->pluck('name_code', 'codePekerja');
        $data['role'] = Roles::pluck('name', 'id');
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
        $this->validate($request, [
            'name' => 'bail|required|min:2|max:255',
            'username' => 'required|max:255|unique:users',
            'roles' => 'required|min:1',
            'codePekerja' => 'required|integer',
            'password' => 'required|min:6|confirmed'
        ]);

        // hash password
        $request->merge(['password' => bcrypt($request->get('password')), 'password_decrypt' => $request->get('password')]);

        // Create the user
        if ( $user = User::create($request->except('roles', 'permissions')) ) {

            $this->syncPermissions($request, $user);

            flash('User has been created.');

        } else {
            flash()->error('Unable to create user.');
        }

        return redirect()->route('usermgmt.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $pekerja = User::find($id)->pekerja;

        if (Auth::user()->hasRole('Super Admin')) {
            $forAdmin = "
                <div class='row'>
                    <div class='col-sm'>
                        Password
                    </div>
                    <div class='col-sm'>
                        ".$user['password_decrypt']."
                    </div>
                </div>
            ";
        }else { $forAdmin = ""; }
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
                    ".$forAdmin."
                    <div class='row'>
                        <div class='col-sm'>
                            Kode Pekerja
                        </div>
                        <div class='col-sm'>
                            ".$pekerja['codePekerja']."
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm'>
                            Nama Pekerja
                        </div>
                        <div class='col-sm'>
                            ".$pekerja['namaPekerja']."
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

        $data['user'] = User::find($param[0]);

        $data['roles'] = Roles::pluck('name', 'id');

        $data['permissions'] = Permission::all('name', 'id');
        
        $data['listPekerja'] = Pekerja::all()->sortBy('namaPekerja')->pluck('name_code', 'codePekerja');

        $data['pekerja'] = User::find($param[0])->pekerja;
        
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
        
        if ($request->status == 'detail') {
            # code...
            $this->validate($request, [
                'name' => 'bail|required|min:2|max:255',
                'username' => 'required|max:255|unique:users',
                'roles' => 'required|min:1',
                'codePekerja' => 'required|integer',
            ]);
            
        }

        if ($request->status == 'password') {
            # code...
            $this->validate($request, [
                'old_password' => 'required|string|min:6',
                'new_password' => 'required|string|min:6|confirmed'
            ]);

            $hashedPassword = DB::table('users')->where('id', '=', $id)->value('password');
            if (!Hash::check($request->old_password, $hashedPassword)) {
                // The passwords not match...
                return back()->with('password', 'Password lama salah');
            }
        }

        // Get the user
        $user = User::findOrFail($id);

        // Update user
        $user->fill($request->except('roles', 'permissions'));

        // check for password change
        if($request->get('new_password')) {
            $user->password = bcrypt($request->get('new_password'));
            $user->password_decrypt = $request->get('new_password');
        }

        // Handle the user roles
        $this->syncPermissions($request, $user);

        $user->save();

        flash()->success('User has been updated.');

        return redirect()->route('usermgmt.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ( Auth::user()->id == $id ) {
            flash()->warning('Deletion of currently logged in user is not allowed :(')->important();
            return redirect()->back();
        }

        if( User::findOrFail($id)->delete() ) {
            flash()->success('User has been deleted');
        } else {
            flash()->success('User not deleted');
        }

        return redirect()->back();
    }

    public function postRoleDropdown(Request $request)
    {
        # code...
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

        if ($request->id == 7) { # kawil
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

    public function callDropdownEdit(Request $request)
    {
        # code...
        $codePekerja = $this->removeWhitespace2(DB::table('EWS_VW_DETAIL_USER')
            ->where('id', '=', $request->idUser)
            ->first());

        if ($request->idRole == 8) { # mandor
            # code...
            $data = DB::table('EWS_VW_DETAIL_MANDOR')
                ->orderBy('namaPekerja', 'asc')
                ->get();
            $listData = $this->removeWhitespace($data);
            $return = '<option value="">Pilih Kode Pekerja...</option>';
            foreach($listData as $temp) 
                if ($temp['codePekerja'] == $codePekerja['codePekerja']) {
                    # code...
                    $return .= "<option value=".$temp['codePekerja']." selected='true'>".$temp['namaPekerja']." [".$temp['codeMandor']."]</option>";
                }else
                {
                    $return .= "<option value=".$temp['codePekerja'].">".$temp['namaPekerja']." [".$temp['codeMandor']."]</option>";
                }
            return $return;
        }

        if ($request->idRole == 7) { # kawil
            # code...
            $data = DB::table('EWS_PEKERJA')
                ->orderBy('namaPekerja', 'asc')
                ->get();
            $listData = $this->removeWhitespace($data);
            $return = '<option value="">Pilih Kode Pekerja...</option>';
            foreach($listData as $temp) 
                if ($temp['idRole'] != NULL) {
                    # code...
                    if ($temp['codePekerja'] == $codePekerja['codePekerja']) {
                        # code...
                        $return .= "<option value=".$temp['codePekerja']." selected='true'>".$temp['namaPekerja']."</option>";
                    }else{
                        $return .= "<option value=".$temp['codePekerja']." disabled>".$temp['namaPekerja']."</option>";
                    }
                }else{
                    $return .= "<option value=".$temp['codePekerja'].">".$temp['namaPekerja']."</option>";
                }
            return $return;
        }
    }

    /**
     * Sync roles and permissions
     *
     * @param Request $request
     * @param $user
     * @return string
     */
    private function syncPermissions(Request $request, $user)
    {
        // Get the submitted roles
        $roles = $request->get('roles', []);
        $permissions = $request->get('permissions', []);

        // Get the roles
        $roles = Roles::find($roles);

        // check for current role changes
        if( ! $user->hasAllRoles( $roles ) ) {
            // reset all direct permissions for user
            $user->permissions()->sync([]);
        } else {
            // handle permissions
            $user->syncPermissions($permissions);
        }

        $user->syncRoles($roles);

        return $user;
    }
}
