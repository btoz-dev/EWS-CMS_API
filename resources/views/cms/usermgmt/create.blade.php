@extends('layouts.cmsApp')

@section('stylesheet')
<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
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
            @if ($errors->has('name')) <p class="form-text">{{ $errors->first('name') }}</p> @endif
        </div>

        <div class="form-group">
            <label for="usernameInput">Username</label>
            <input type="text" class="form-control" id="usernameInput" name="username" placeholder="username" maxlength="255">
            @if ($errors->has('username')) <p class="form-text">{{ $errors->first('username') }}</p> @endif
        </div>

        <div class="form-group">
            {!! Form::label('roles[]', 'Roles') !!}
            {!! Form::select('roles[]', $role, isset($user) ? $user->roles->pluck('id')->toArray() : null,  ['class' => 'form-control', 'multiple']) !!}
            @if ($errors->has('roles')) <p class="form-text">{{ $errors->first('roles') }}</p> @endif
        </div>

        <div class="form-group">
            {!! Form::label('codePekerja', 'Pekerja') !!}
            {!! Form::select('codePekerja', $pekerja, null,  ['placeholder' => 'Nothing Selected...', 'class' => 'selectpicker form-control']) !!}
            @if ($errors->has('codePekerja')) <p class="form-text">{{ $errors->first('codePekerja') }}</p> @endif
        </div>

        <div class="form-group">
            <label for="passwordInput">Password</label>
            <input type="password" class="form-control" id="passwordInput" name="password">
            @if ($errors->has('password')) <p class="form-text">{{ $errors->first('password') }}</p> @endif
        </div>

        <div class="form-group">
            <label for="confirmPasswordInput">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPasswordInput" name="password_confirmation">
            @if ($errors->has('password_confirmation')) <p class="form-text">{{ $errors->first('password_confirmation') }}</p> @endif
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>

    </form>
@endsection

@section('script')
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.fn.selectpicker.Constructor.BootstrapVersion = '4';

        $('#codePekerja').selectpicker({
            liveSearch : true,
            size : 5
        });
    </script>
@endsection
