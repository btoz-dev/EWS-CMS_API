@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <div class="card rounded-0">
          <h5 class="card-header">Mandor Plantcare Report</h5>
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
                        <th>Kode RKH</th>
                        <th>Tgl RKH</th>
                        <th>Mandor</th>
                        <th>TK</th>
                        <th>Pekerjaan</th>
                        <th>Kode Blok</th>
                        <th>Kode Tanaman</th>
                        <th>Catatan Mandor</th>
                        <th>Date</th>
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
        $.fn.dataTable.ext.errMode = 'none';
        var oTable = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('mandorPlantcareReport.index') }}',
                data: function (d) {
                    d.date_aw = $('input[name=date_aw]').val();
                    d.date_ak = $('input[name=date_ak]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'rkhCode', name: 'rkhCode'},
                {data: 'rkhDate2', name: 'rkhDate2'},
                {data: 'mandor', name: 'mandor'},
                {data: 'tk', name: 'tk'},
                {data: 'Description', name: 'Description'},
                {data: 'codeBlok', name: 'codeBlok'},
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'mandorNote', name: 'mandorNote'},
                {data: 'created_at2', name: 'created_at2'},
            ],
        });

        var headings = [];
        $(oTable.table().header()).find('th').each(function () {
          // access each tr's tds from here using $(this)
          var head = $(this).text()
          headings.push(head);
        });

        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
            var firstDate = new Date($('#date_aw').val());
            var secondDate = new Date($('#date_ak').val());

            var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
            if (diffDays > 31) {
                alert('\n WARNING! \n Harap memilih tanggal dibawah 31 hari. \n Dikarenakan data yang besar, dapat menyebabkan kesalahan sistem');
            }
            else {
                oTable.draw();
            }
            // console.log(diffDays);
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
            var url = '{{route('exportMandor')}}';
            var params = { 
                    heading: headings, 
                    job: "PLANTCARE", 
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
