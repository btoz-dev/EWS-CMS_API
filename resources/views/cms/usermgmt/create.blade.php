@extends('layouts.cmsApp')

@section('stylesheet')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.7/dist/css/bootstrap-select.min.css">
@endsection

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
            <label for="nameInput">Name</label>
            <input type="text" class="form-control" id="nameInput" name="name" placeholder="name" maxlength="255">
        </div>

        <div class="form-group">
            <label for="usernameInput">Username</label>
            <input type="text" class="form-control" id="usernameInput" name="username" placeholder="username" maxlength="255">
        </div>

        <div class="form-group">
            <label for="emailInput">E-Mail Address</label>
            <input type="email" class="form-control" id="emailInput" name="email" placeholder="name@example.com">
        </div>

        <div class="form-group">
            <label for="roleInput">Role</label>
            <select class="form-control selectpicker" id="roleInput" name="role">
                <option value="">Pilih Role User</option>
                @foreach($role as $role)
                    <option value="{{$role['id']}}">{{$role['namaRole']}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="pekerjaInput" id="pekerjaInputLabel">Nama Pekerja</label>
            <select class="form-control selectpicker" id="pekerjaInput" name="pekerja">
            </select>
        </div>

        <div class="form-group">
            <label for="passwordInput">Password</label>
            <input type="password" class="form-control" id="passwordInput" name="password">
        </div>

        <div class="form-group">
            <label for="confirmPasswordInput">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPasswordInput" name="password_confirmation">
        </div>

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
            if ($(this).val() == 8 || $(this).val() == 7) {
                $.post('{{route('usermgmt.postRoleDropdown')}}',{id: $(this).val()}, function (e) {
                    // body...
                    console.log(e);
                    $('#pekerjaInput').prop('disabled', false);
                    $('#pekerjaInput').html(e);
                    $('#pekerjaInput').selectpicker('refresh');
                    $('#pekerjaInput').selectpicker('show');
                    document.getElementById("pekerjaInputLabel").style.display = 'block';
                })
            }else{
                $('#pekerjaInput').html('');
                $('#pekerjaInput').selectpicker('refresh');
                $('#pekerjaInput').prop('disabled', true);
                $('#pekerjaInput').selectpicker('hide');
                document.getElementById("pekerjaInputLabel").style.display = 'none';
            }
        })
    </script>
@endsection
