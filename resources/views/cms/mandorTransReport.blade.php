@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <div class="card rounded-0">
          <h5 class="card-header">Mandor Trans Report</h5>
          <div class="card-body">
            <form method="POST" id="search-form" class="form-inline" role="form">

                <div class="form-group">
                    <label for="date_aw">Dari</label>
                    <input type="date" class="form-control mx-sm-3" name="date_aw" id="date_aw" placeholder="search tanggal dari (MM/DD/YYYY)">
                </div>
                <div class="form-group">
                    <label for="date_ak">Sampai</label>
                    <input type="date" class="form-control mx-sm-3" name="date_ak" id="date_ak" placeholder="search tanggal ke (MM/DD/YYYY)">
                </div>

                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
          </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode RKH</th>
                        <th>Mandor</th>
                        <th>TK</th>
                        <th>Pekerjaan</th>
                        <th>Kode Blok</th>
                        <th>Kode Tanaman</th>
                        <th>Catatan Mandor</th>
                        <th>Total Hand</th>
                        <th>Total Finger</th>
                        <th>Total Leaf</th>
                        <th>Ribbon Color</th>
                        <th>Skimming Size</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var oTable = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('mandorTransReport.index') }}',
                data: function (d) {
                    d.date_aw = $('input[name=date_aw]').val();
                    d.date_ak = $('input[name=date_ak]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'rkhCode', name: 'rkhCode'},
                {data: 'mandor', name: 'mandor'},
                {data: 'tk', name: 'tk'},
                {data: 'Description', name: 'Description'},
                {data: 'codeBlok', name: 'codeBlok'},
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'mandorNote', name: 'mandorNote'},
                {data: 'totalHand', name: 'totalHand'},
                {data: 'totalFinger', name: 'totalFinger'},
                {data: 'totalLeaf', name: 'totalLeaf'},
                {data: 'ribbonColor', name: 'ribbonColor'},
                {data: 'skimmingSize', name: 'skimmingSize'},
                {data: 'created_at', name: 'created_at'},
            ]
        });

        $('#search-form').on('submit', function(e) {
            oTable.draw();
            e.preventDefault();
        });

        $('#date_aw').on('change', function(e) {
            console.log($(this).val());
            console.log($('#date_ak').val());
            if ($(this).val() > $('#date_ak').val()) {
                $('#date_ak').val($(this).val());
            }
        })

        $('#date_ak').on('change', function(e) {
            console.log($(this).val());
            if ($(this).val() < $('#date_aw').val()) {
                $('#date_aw').val($(this).val());
            }
        })
    </script>
@endsection
