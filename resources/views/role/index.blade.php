@extends('layouts.cmsApp')

@section('title', 'Roles & Permissions')

@section('content')

    <!-- Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="roleModal" aria-labelledby="roleModalLabel">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'post']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- name Form Input -->
                    <div class="form-group @if ($errors->has('name')) has-error @endif">
                        {!! Form::label('name', 'Name') !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Role Name']) !!}
                        @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- Submit Form Button -->
                    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="container">
        <hr>
        <form class="form-inline">
            <div class="form-group mb-2">
                <h2>Role Management</h2>
            </div>
            <!-- <div class="form-group mx-sm-3 mb-2">
                @can('add_roles')
                    <a href="#" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#roleModal"> <i class="glyphicon glyphicon-plus"></i> New Role</a>
                @endcan
            </div> -->
            @if (session('alert'))
                <div class="alert alert-info alert-dismissible" role="alert">
                    {{ session('alert') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </form>
        <hr>
        @forelse ($roles as $role)
            {!! Form::model($role, ['method' => 'PUT', 'route' => ['roles.update',  $role->id ], 'class' => 'mb-3']) !!}

            @if($role->name === 'Admin')
                @include('shared._permissions', [
                            'title' => $role->name .' Permissions',
                            'options' => ['disabled'] ])
            @else
                @include('shared._permissions', [
                            'title' => $role->name .' Permissions',
                            'model' => $role ])
                @can('edit_roles')
                    {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                @endcan
            @endif

            {!! Form::close() !!}

        @empty
            <p>No Roles defined, please run <code>php artisan db:seed</code> to seed some dummy data.</p>
        @endforelse
    </div>

@endsection