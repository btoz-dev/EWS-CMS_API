@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <h2>Reports</h2>
        <hr>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode RKH</th>
                        <th>Tanggal RKH</th>
                        <th>Kode Mandor</th>
                        <th>Aktifitas</th>
                        <th>Kode Blok</th>
                        <th>Baris Start</th>
                        <th>Baris End</th>
                        <th>Total Pokok</th>
                        <th>Realisasi Pokok</th>
                        <th>Persentase Pokok</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('rkmReport.index') }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'rkhCode', name: 'rkhCode'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'mandorCode', name: 'mandorCode'},
                {data: 'codeAlojob', name: 'codeAlojob'},
                {data: 'codeBlok', name: 'codeBlok'},
                {data: 'barisStart', name: 'barisStart'},
                {data: 'barisEnd', name: 'barisEnd'},
                {data: 'totalPokok', name: 'totalPokok'},
                {data: 'pokokDone', name: 'pokokDone'},
                {data: 'persentase', name: 'persentase'},
            ]
        });
    </script>
@endsection
