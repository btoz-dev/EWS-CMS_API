@extends('layouts.cmsApp')

@section('content')
    <div class="row mt-3">
        <div class="col">
            <h2>CLT Management | Ubah CLT</h2>
        </div>
    </div>
    
    <form method="POST" action="{{ route('clt.update', $id) }}">
        {{ method_field('PUT') }}
        {{ csrf_field() }}

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    {{$error}}
                @endforeach
            </div>
        @endif

        <div class="form-group">
            <label for="nameInput">Nama Produk</label>
            <input type="text" class="form-control" id="nameInput" name="name" placeholder="name" maxlength="255" value="{{$clt['desc']}}">
            @if ($errors->has('name')) <p class="form-text">{{ $errors->first('name') }}</p> @endif
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>

    </form>
@endsection