@extends('layouts.cmsApp')

@section('content')
    <div class="row mt-3">
        @can('add_apk')
            <div class="col">
                <h2>Upload APK RKH File</h2>
            </div>
        @endcan

        @can('view_apk')
            <div class="col">
                <h2>Download APK RKH File</h2>
            </div>
        @endcan
    </div>

    <div class="row mt-3">
        @can('add_apk')
            <div class="col">
                <form method="POST" action="{{ route('uploadApkRKH') }}" enctype="multipart/form-data">
                    @if (session('alertRKH'))
                        <div class="alert alert-info alert-dismissible" role="alert">
                            {{ session('alertRKH') }}
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
                        <textarea class="form-control" id="logapk" rows="3" name="logapk">{{ $logTxtRKH }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>

                </form>
            </div>
        @endcan

        @can('view_apk')
            <div class="col">
                <a class="btn btn-primary" href="{{ asset('storage/apkfile/ews_rkh.apk') }}" role="button">Download APK</a>
            </div>
        @endcan
    </div>

    <div class="row mt-3">
        @can('add_apk')
            <div class="col">
                <h2>Upload APK PH File</h2>
            </div>
        @endcan

        @can('view_apk')
            <div class="col">
                <h2>Download APK PH File</h2>
            </div>
        @endcan
    </div>

    <div class="row mt-3">
        @can('add_apk')
            <div class="col">
                <form method="POST" action="{{ route('uploadApkRKH') }}" enctype="multipart/form-data">
                    @if (session('alertPH'))
                        <div class="alert alert-info alert-dismissible" role="alert">
                            {{ session('alertPH') }}
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
                        <textarea class="form-control" id="logapk" rows="3" name="logapk">{{ $logTxtPH }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>

                </form>
            </div>
        @endcan

        @can('view_apk')
            <div class="col">
                <a class="btn btn-primary" href="{{ asset('storage/apkfile/ews_ph.apk') }}" role="button">Download APK</a>
            </div>
        @endcan
    </div>
@endsection
