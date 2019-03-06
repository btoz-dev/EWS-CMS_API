@extends('layouts.cmsApp')

@section('content')
    <div class="row mt-3">
        <div class="col">
            <h2>User Management | Tambah User</h2>
        </div>
    </div>
    
    <form method="POST" action="{{ route('usermgmt.store') }}">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    {{$error}}
                @endforeach
            </div>
        @endif
        {{ csrf_field() }}
        <div class="form-group">
            <label for="usernameInput">Username</label>
            <input type="text" class="form-control" id="usernameInput" name="username" placeholder="username" maxlength="255">
        </div>
        <div class="form-group">
            <label for="emailInput">Email</label>
            <input type="email" class="form-control" id="emailInput" name="email" placeholder="name@example.com">
        </div>
        <div class="form-group">
            <label for="pekerjaInput">Nama Pekerja</label>
            <select class="form-control" id="pekerjaInput" name="pekerja">
                <option>Pilih Nama Pekerja</option>
                @foreach($pekerja as $pekerja)
                    <option value="{{$pekerja['codePekerja']}}">{{$pekerja['namaPekerja']}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="roleInput">Role</label>
            <select class="form-control" id="roleInput" name="role">
                <option>Pilih Role User</option>
                @foreach($role as $role)
                    <option value="{{$role['id']}}">{{$role['namaRole']}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
