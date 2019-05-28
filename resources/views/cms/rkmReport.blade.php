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
                    <input type="date" class="form-control mx-sm-3" name="date_aw" id="date_aw" placeholder="{{ date('m/d/Y') }}" value="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label for="date_ak">Sampai</label>
                    <input type="date" class="form-control mx-sm-3" name="date_ak" id="date_ak" placeholder="{{ date('m/d/Y') }}" value="{{ date('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <button type="submit" class="form-control mx-sm-3 btn btn-primary">Cari</button>
                </div>

                <div class="form-group">
                    <button type="button" class="form-control mx-sm-3 btn btn-success" name="export">Export</button>
                    <div class="spinner-border text-warning" role="status" style="display:none;" id="loading-export">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

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
                        <th>Sisa Pokok</th>
                        <th>Persentase Realisasi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ajaxSend(function(e,x,o){
            $("#loading-export").show();
        });
        $(document).ajaxStop(function(e,x,o){
            $("#loading-export").hide();
        });
        
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
                {data: 'pokokNDone', name: 'pokokNDone'},
                {data: 'persentase', name: 'persentase'},
            ]
        });

        var headings = [];
        $(oTable.table().header()).find('th').each(function () {
          // access each tr's tds from here using $(this)
          var head = $(this).text()
          headings.push(head);
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

        $('button[name="export"]').on('click', function(e) {
            var url = '{{route('exportRKM')}}';
            var params = { 
                    heading: headings, 
                    date_aw: $('input[name=date_aw]').val(), 
                    date_ak: $('input[name=date_ak]').val() 
                };

            // downloadFromAjaxPost(url, params);

            $.ajax({
                type: "POST",
                url: url,
                data: params,
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response, status, request) {
                    var data = new Blob([response]);

                    if (navigator.msSaveOrOpenBlob) {
                        navigator.msSaveOrOpenBlob(data, "report.xlsx");
                    } else {
                        var a = document.createElement('a');
                        var url = window.URL.createObjectURL(response);
                        
                        a.setAttribute("type", "hidden"); // make it hidden if needed
                        a.href = url;
                        a.download = '';

                        // Add the element to the DOM
                        document.body.appendChild(a); //Support for firefox

                        a.click();
                        window.URL.revokeObjectURL(url);
                        a.remove();
                    }
                }
            });
        })
    </script>
@endsection
