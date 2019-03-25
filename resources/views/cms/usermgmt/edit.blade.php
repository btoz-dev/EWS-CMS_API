@extends('layouts.cmsApp')

@section('stylesheet')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.7/dist/css/bootstrap-select.min.css">
@endsection

@section('content')
    <div class="row mt-3">
        <div class="col">
            <h2>User Management | Ubah User</h2>
        </div>
    </div>
    
    <form method="POST" action="{{ route('usermgmt.update', $id) }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        @if(session('password'))
            <div class="alert alert-danger" role="alert">
                {{session('password')}}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    {{$error}}
                @endforeach
            </div>
        @endif

        @if($status == 'detail')
            <input type="hidden" name="status" value="{{$status}}">
            <div class="form-group">
                <label for="nameInput">Name</label>
                <input type="text" class="form-control" id="nameInput" name="name" placeholder="name" maxlength="255" value="{{$user['name']}}">
            </div>

            <div class="form-group">
                <label for="usernameInput">Username</label>
                <input type="text" class="form-control" id="usernameInput" name="username" placeholder="username" maxlength="255" value="{{$user['username']}}">
            </div>

            <div class="form-group">
                <label for="emailInput">E-Mail Address</label>
                <input type="email" class="form-control" id="emailInput" name="email" placeholder="name@example.com" value="{{$user['email']}}">
            </div>

            <div class="form-group">
                <label for="roleInput">Role</label>
                <select class="form-control selectpicker" id="roleInput" name="role">
                    <option value="0">Pilih Role User</option>
                    @foreach($role as $role)
                        @if ($user['idRole'] == $role['id'])
                            <option value="{{$role['id']}}" selected="true">{{$role['namaRole']}}</option>
                        @else
                            <option value="{{$role['id']}}">{{$role['namaRole']}}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="pekerjaInput">Kode Pekerja</label>
                <select class="form-control selectpicker" id="pekerjaInput" name="pekerja">
                    @foreach($pekerja as $pekerja)
                        @if ($user['codePekerja'] == $pekerja['codePekerja'])
                            <option value="{{$pekerja['codePekerja']}}" selected="true">{{$pekerja['namaPekerja']}} [{{$pekerja['codeMandor']}}]</option>
                        @else
                            <option value="{{$pekerja['codePekerja']}}">{{$pekerja['namaPekerja']}} [{{$pekerja['codeMandor']}}]</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

        @if($status == 'password')
            <input type="hidden" name="status" value="{{$status}}">

            <div class="form-group">
                <label for="newPasswordInput">Old Password</label>
                <input type="password" class="form-control" id="newPasswordInput" name="old_password">
            </div>

            <div class="form-group">
                <label for="oldasswordInput">New Password</label>
                <input type="password" class="form-control" id="oldasswordInput" name="new_password">
            </div>

            <div class="form-group">
                <label for="confirmPasswordInput">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPasswordInput" name="new_password_confirmation">
            </div>
        @endif

        <button type="submit" class="btn btn-primary">Submit</button>

    </form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.7/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.fn.selectpicker.Constructor.BootstrapVersion = '4';

        $('#roleInput, #pekerjaInput').selectpicker({
            liveSearch : true,
            size : 5
        });

        $('#roleInput').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            // body...
            $.post('{{route('usermgmt.postRoleDropdown')}}',{id: $(this).val()}, function (e) {
                // body...
                console.log(e);
                $('#pekerjaInput').html(e);
                $('#pekerjaInput').selectpicker('refresh');
            })
        })
    </script>
@endsection
