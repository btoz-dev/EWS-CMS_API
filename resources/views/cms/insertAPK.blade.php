@extends('layouts.cmsApp')

@section('content')
    <div class="row mt-3">
        <div class="col">
            <h2>Upload APK File</h2>
        </div>

        <div class="col">
            <h2>Download APK File</h2>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <form method="POST" action="{{ route('uploadapk') }}" enctype="multipart/form-data">
                @if (session('alert'))
                    <div class="alert alert-info alert-dismissible" role="alert">
                        {{ session('alert') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{ csrf_field() }}

                <div class="form-group">
                    <input type="file" class="form-control-file" id="exampleFormControlFile1" name="file">
                </div>

                <div class="form-group">
                    <label for="logapk">Log APK</label>
                    <textarea class="form-control" id="logapk" rows="3" name="logapk">{{ $logTxt }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>

            </form>
        </div>

        <div class="col">
            <a class="btn btn-primary" href="{{ asset('storage/apkfile/ews.apk') }}" role="button">Download APK</a>
        </div>
    </div>
@endsection
