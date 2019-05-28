@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <div class="card rounded-0">
          <h5 class="card-header">Packing House Berat Tandan Report</h5>
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
                        <th>Kode Tanaman</th>
                        <th>Berat Bruto</th>
                        <th>Berat Bonggol</th>
                        <th>Berat Bersih</th>
                        <th>Catatan Bruto</th>
                        <th>Catatan Bonggol</th>
                        <th>Tanggal Timbang Bruto</th>
                        <th>Tanggal Timbang Bonggol</th>
                        <th>Penimbang Bruto</th>
                        <th>Penimbang Bonggol</th>
                        <th>TK Bruto</th>
                        <th>TK Bonggol</th>
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
            // buttons: [
            //     {
            //         extend: 'excel',
            //         text: 'Save current page',
            //         exportOptions: {
            //             modifier: {
            //                 page: 'all'
            //             }
            //         }
            //     }
            // ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('phbtReport.index') }}',
                data: function (d) {
                    d.date_aw = $('input[name=date_aw]').val();
                    d.date_ak = $('input[name=date_ak]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'brutoBerat', name: 'brutoBerat'},
                {data: 'bonggolBerat', name: 'bonggolBerat'},
                {data: 'beratBersih', name: 'beratBersih'},
                {data: 'brutoNote', name: 'brutoNote'},
                {data: 'bonggolNote', name: 'bonggolNote'},
                {data: 'brutoDate', name: 'brutoDate'},
                {data: 'bonggolDate', name: 'bonggolDate'},
                {data: 'userBruto', name: 'userBruto'},
                {data: 'userBonggol', name: 'userBonggol'},
                {data: 'TKBruto', name: 'TKBruto'},
                {data: 'TKBonggol', name: 'TKBonggol'},
            ],
            // initComplete : function () {
            //     oTable.buttons().container()
            //            .appendTo( $('#search-form .form-group:eq(3)'));
            // }
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
        });

        $('#date_ak').on('change', function(e) {
            console.log($(this).val());
            if ($(this).val() < $('#date_aw').val()) {
                $('#date_aw').val($(this).val());
            }
        });

        $('button[name="export"]').on('click', function(e) {
            var url = '{{route('exportPH')}}';
            var params = { 
                    heading: headings, 
                    job: "BT", 
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
        });
    </script>
@endsection
