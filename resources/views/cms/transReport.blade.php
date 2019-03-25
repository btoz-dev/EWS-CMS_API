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
                        <th>Mandor</th>
                        <th>Tukang</th>
                        <th>Pekerjaan</th>
                        <th>Kode Tanaman</th>
                        <th>Catatan Mandor</th>
                        <th>Total Hand</th>
                        <th>Total Finger</th>
                        <th>Total Leaf</th>
                        <th>Ribbon Color</th>
                        <th>Skimming Size</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
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
            ajax: '{{ route('transReport.index') }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'rkhCode', name: 'rkhCode'},
                {data: 'namaMandor', name: 'namaMandor'},
                {data: 'namaTukang', name: 'namaTukang'},
                {data: 'Description', name: 'Description'},
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'mandorNote', name: 'mandorNote'},
                {data: 'totalHand', name: 'totalHand'},
                {data: 'totalFinger', name: 'totalFinger'},
                {data: 'totalLeaf', name: 'totalLeaf'},
                {data: 'ribbonColor', name: 'ribbonColor'},
                {data: 'skimmingSize', name: 'skimmingSize'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'waktu', name: 'waktu'},
            ]
        });
    </script>
@endsection
