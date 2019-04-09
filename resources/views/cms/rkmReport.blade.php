@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <div class="card rounded-0">
          <h5 class="card-header">RKM Report</h5>
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
        <!-- <h2>Reports</h2>
        <hr> -->
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode RKH</th>
                        <th>Tanggal RKH</th>
                        <th>Nama Mandor</th>
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
        var oTable = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('rkmReport.index') }}',
                data: function (d) {
                    d.date_aw = $('input[name=date_aw]').val();
                    d.date_ak = $('input[name=date_ak]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'rkhCode', name: 'rkhCode'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'namaPekerja', name: 'namaPekerja'},
                {data: 'Description', name: 'Description'},
                {data: 'codeBlok', name: 'codeBlok'},
                {data: 'barisStart', name: 'barisStart'},
                {data: 'barisEnd', name: 'barisEnd'},
                {data: 'totalPokok', name: 'totalPokok'},
                {data: 'pokokDone', name: 'pokokDone'},
                {data: 'persentase', name: 'persentase'},
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
