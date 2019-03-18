<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UsermgmtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('users')
            ->join('EWS_PEKERJA', 'users.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
            ->join('EWS_ROLE_USER', 'EWS_PEKERJA.idRole', '=', 'EWS_ROLE_USER.id')
            ->select('users.id', 'users.username', 'users.email', 'EWS_PEKERJA.codePekerja', 'EWS_PEKERJA.namaPekerja', 'EWS_ROLE_USER.namaRole', 'EWS_ROLE_USER.descRole')
            ->get();
            $users = $this->removeWhitespace($query);
            return DataTables::of($users)->make(true);
        }

        // $users = $this->removeWhitespace(DB::table('users')
        //     ->join('EWS_PEKERJA', 'users.codePekerja', '=', 'EWS_PEKERJA.codePekerja')
        //     ->join('EWS_ROLE_USER', 'EWS_PEKERJA.idRole', '=', 'EWS_ROLE_USER.id')
        //     ->select('users.id', 'users.username', 'users.email', 'EWS_PEKERJA.codePekerja', 'EWS_PEKERJA.namaPekerja', 'EWS_ROLE_USER.namaRole', 'EWS_ROLE_USER.descRole')
        //     ->get());

        // $data['users'] = $users;
        return view('cms.usermgmt');
        // return view('cms.usermgmt', $data);
        // return DataTables::of($users)->make(true);
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

        $rules = [
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'pekerja' => 'required|integer|unique:users,codePekerja',
            'role' => 'required|integer'
        ];
        // $request->validate([
        //     'username' => 'required|between:0,255',
        //     'email' => 'required|email||between:0,255',
        //     'pekerja' => 'required|integer',
        //     'role' => 'required|integer'
        // ]);
        $validator = Validator::make($request->all(), $rules, $messages)->validate();
        return $request;
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
