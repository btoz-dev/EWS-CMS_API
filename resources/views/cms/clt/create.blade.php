@extends('layouts.cmsApp')

@section('content')
    <div class="row mt-3">
        <div class="col">
            <h2>CLT Management | Tambah CLT</h2>
        </div>
    </div>
    
    <form method="POST" action="{{ route('clt.store') }}">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    {{$error}}
                @endforeach
            </div>
        @endif

        {{ csrf_field() }}

        <div class="form-group">
            <label for="nameInput">Nama Produk</label>
            <input type="text" class="form-control" id="nameInput" name="name" placeholder="name" maxlength="255">
            @if ($errors->has('name')) <p class="form-text">{{ $errors->first('name') }}</p> @endif
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>

    </form>
@endsection