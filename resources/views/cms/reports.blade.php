@extends('layouts.cmsApp')

@section('content')
    <h2>Reports</h2>
    @isset($rkm)
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode RKH</th>
                        <th>Tanggal RKH</th>
                        <th>Kode Mandor</th>
                        <th>Kode Blok</th>
                        <th>Baris Mulai</th>
                        <th>Baris Akhir</th>
                        <th>Nama Job</th>
                        <th>Nama Sub Job</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rkm as $value)
                        <tr>
                            <td>{{$counter++}}</td>
                            <td>{{$value['rkhCode']}}</td>
                            <td>{{$value['rkhDate']}}</td>
                            <td>{{$value['mandorCode']}}</td>
                            <td>{{$value['codeBlok']}}</td>
                            <td>{{$value['barisStart']}}</td>
                            <td>{{$value['barisEnd']}}</td>
                            <td>{{$value['parentJobName']}}</td>
                            <td>{{$value['childJobName']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
    
@endsection
