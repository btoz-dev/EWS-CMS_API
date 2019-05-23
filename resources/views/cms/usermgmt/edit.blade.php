@extends('layouts.cmsApp')

@section('stylesheet')
<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
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
                @if ($errors->has('roles')) <p class="form-text">{{ $errors->first('roles') }}</p> @endif
            </div>

            <div class="form-group">
                <label for="usernameInput">Username</label>
                <input type="text" class="form-control" id="usernameInput" name="username" placeholder="username" maxlength="255" value="{{$user['username']}}">
                @if ($errors->has('roles')) <p class="form-text">{{ $errors->first('roles') }}</p> @endif
            </div>

            <div class="form-group">
                {!! Form::label('roles[]', 'Roles') !!}
                {!! Form::select('roles[]', $roles, isset($user) ? $user->roles->pluck('id')->toArray() : null,  ['class' => 'form-control', 'multiple']) !!}
                @if ($errors->has('roles')) <p class="form-text">{{ $errors->first('roles') }}</p> @endif
            </div>

            <div class="form-group">
                {!! Form::label('codePekerja', 'Pekerja') !!}
                {!! Form::select('codePekerja', $listPekerja, $pekerja->codePekerja,  ['class' => 'selectpicker form-control']) !!}
                @if ($errors->has('codePekerja')) <p class="form-text">{{ $errors->first('codePekerja') }}</p> @endif
            </div>
        @endif

        @if($status == 'password')
            <input type="hidden" name="status" value="{{$status}}">

            <div class="form-group">
                <label for="newPasswordInput">Old Password</label>
                <input type="password" class="form-control" id="newPasswordInput" name="old_password">
                @if ($errors->has('old_password')) <p class="form-text">{{ $errors->first('old_password') }}</p> @endif
            </div>

            <div class="form-group">
                <label for="oldasswordInput">New Password</label>
                <input type="password" class="form-control" id="oldasswordInput" name="new_password">
                @if ($errors->has('new_password')) <p class="form-text">{{ $errors->first('new_password') }}</p> @endif
            </div>

            <div class="form-group">
                <label for="confirmPasswordInput">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPasswordInput" name="new_password_confirmation">
                @if ($errors->has('new_password_confirmation')) <p class="form-text">{{ $errors->first('new_password_confirmation') }}</p> @endif
            </div>
        @endif

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
