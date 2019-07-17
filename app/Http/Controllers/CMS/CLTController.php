<?php

namespace App\Http\Controllers\CMS;

use DataTables;
use DB;
use App\CLT;
use App\Roles;
use App\Pekerja;
use App\Permission;
use App\Authorizable;
use Illuminate\Http\Request;
use App\Http\Controllers\CMSController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CLTController extends CMSController
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

            $produk = CLT::all();

            return DataTables::of($produk)
                ->addColumn('aksi', function ($produk)
                {
                    $user = Auth::user();
                    $userid = Auth::id();

                    if ($user->hasPermissionTo('view_clt')) {
                        $view = "<button type='button' class='btn btn-secondary btn-success' id='showDetail' data-id=".$produk["id"].">Show</button>";
                    } else { $view = ""; }
                    if ($user->hasPermissionTo('edit_clt')) {
                        $edit = "<a href=".route("clt.edit", ["id" => $produk["id"]])." id='edit' class='btn btn-secondary btn-info' tabindex='-1' role='button' aria-disabled='false'>Detail</a>";
                    } else { $edit = ""; }
                    if ($user->hasPermissionTo('delete_clt')) {
                        $delete = "<button type='button' class='btn btn-secondary btn-danger' id='penghapusan' data-toggle='modal' data-target='#hapusModal' data-url=".route("clt.destroy", ["id" => $produk["id"]]).">Hapus</button>";
                    } else { $delete = ""; }

                    return "
                    <div class='btn-group' role='group' aria-label='Button group with nested dropdown' id='btn-group-aksi'>
                        ".$view."
                        ".$edit."
                        ".$delete."
                    </div>
                    ";
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('cms.clt.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['clt'] = Pekerja::all();
        return view('cms.clt.create', $data);
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
            'name' => 'required|max:255',
        ]);

        $clt = new CLT;

        $clt->desc = $request->name;

        // Create the produk
        if ( $clt->save() ) {

            flash('Produk has been created.');

        } else {
            flash()->error('Unable to create Produk.');
        }

        return redirect()->route('clt.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $clt = CLT::find($id);

        return "
            <div class='modal-header'>
                <h5 class='modal-title' id='showDetailLabel'>CLT Detail</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <div class='container'>
                    <div class='row'>
                        <div class='col-sm'>
                            Nama Produk
                        </div>
                        <div class='col-sm'>
                            ".$clt['desc']."
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
        $data['id'] = $id;

        $data['clt'] = CLT::find($id);

        return view('cms.clt.edit', $data);
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

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $clt = CLT::find($id);

        $clt->desc = $request->name;

        $clt->save();

        flash()->success('Produk has been updated.');

        return redirect()->route('clt.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if( CLT::findOrFail($id)->delete() ) {
            flash()->success('Produk has been deleted');
        } else {
            flash()->success('Produk not deleted');
        }

        return redirect()->back();
    }
}
