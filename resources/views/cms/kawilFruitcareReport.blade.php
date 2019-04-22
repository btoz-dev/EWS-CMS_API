@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <div class="card rounded-0">
          <h5 class="card-header">Kawil Fruitcare Report</h5>
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

                <div class="form-group">
                    <button type="submit" class="form-control mx-sm-3 btn btn-primary">Cari</button>
                </div>

                <!-- <div class="form-group">
                </div> -->
                
                <div class="form-group">
                    <button type="button" class="form-control mx-sm-3 btn btn-success" name="export">Export</button>
                    <div class="spinner-border text-warning" role="status" style="display:none;" id="loading-export">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

            </form>
          </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kawil</th>
                        <th>Catatan Kawil</th>
                        <th>Date</th>
                        <th>RKH</th>
                        <th>Aktifitas</th>
                        <th>Mandor</th>
                        <th>TK</th>
                        <th>Kode Blok</th>
                        <th>Kode Tanaman</th>
                        <th>Catatan Mandor</th>
                        <th>Jumlah Hand</th>
                        <th>Jumlah Finger</th>
                        <th>Jumlah Daun</th>
                        <th>Ribbon Color</th>
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
                url: '{{ route('kawilFruitcareReport.index') }}',
                data: function (d) {
                    d.date_aw = $('input[name=date_aw]').val();
                    d.date_ak = $('input[name=date_ak]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'kawil', name: 'kawil'},
                {data: 'kawilNote', name: 'kawilNote'},
                {data: 'created_at', name: 'created_at'},
                {data: 'rkhCode', name: 'rkhCode'},
                {data: 'Description', name: 'Description'},
                {data: 'mandor', name: 'mandor'},
                {data: 'tk', name: 'tk'},
                {data: 'codeBlok', name: 'codeBlok'},
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'mandorNote', name: 'mandorNote'},
                {data: 'totalHand', name: 'totalHand'},
                {data: 'totalFinger', name: 'totalFinger'},
                {data: 'totalLeaf', name: 'totalLeaf'},
                {data: 'ribbonColor', name: 'ribbonColor'},
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
            var url = '{{route('exportKawil')}}';
            var params = { 
                    heading: headings, 
                    job: "FRUITCARE", 
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
                    // console.log(response);
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(response);
                    a.href = url;
                    a.download = '';
                    a.click();
                    window.URL.revokeObjectURL(url);
                }
            });
        })
    </script>
@endsection
