@extends('layouts.cmsApp')

@section('content')
    <div class="row mt-3">
        <div class="col">
            <h2>User Management</h2>
        </div>
        <div class="col">
            <a href="{{ url('/usermgmt/create') }}" class="btn btn-primary btn-sm">Tambah User</a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Kode Pekerja</th>
                    <th>Nama Pekerja</th>
                    <th>Role</th>
                    <th>Deskripsi Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $value)
                    <tr>
                        <td>{{$value['id']}}</td>
                        <td>{{$value['username']}}</td>
                        <td>{{$value['email']}}</td>
                        <td>{{$value['codePekerja']}}</td>
                        <td>{{$value['namaPekerja']}}</td>
                        <td>{{$value['namaRole']}}</td>
                        <td>{{$value['descRole']}}</td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-info btn-sm">Ubah</button>
                                <button type="button" class="btn btn-danger btn-sm">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
