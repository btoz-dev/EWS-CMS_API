@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <div class="card rounded-0">
          <h5 class="card-header">Packing House Hitung Tandan Report</h5>
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
                        <th>Kode Tanaman</th>
                        <th>Penghitung</th>
                        <th>TK</th>
                        <th>Hand Class </th>
                        <th>Calibrasi/Hand Class 2 </th>
                        <th>Calibrasi/Hand Class 4 </th>
                        <th>Calibrasi/Hand Class 6 </th>
                        <th>Calibrasi/Hand Class 8 </th>
                        <th>Calibrasi/Hand Class 10 </th>
                        <th>Calibrasi/Hand Class Akhir </th>
                        <th>Finger Length 2 </th>
                        <th>Finger Length 4 </th>
                        <th>Finger Length 6 </th>
                        <th>Finger Length 8 </th>
                        <th>Finger Length 10 </th>
                        <th>Finger Length Akhir </th>
                        <th>Jumlah Finger/Hand Class 2 </th>
                        <th>Jumlah Finger/Hand Class 4 </th>
                        <th>Jumlah Finger/Hand Class 6 </th>
                        <th>Jumlah Finger/Hand Class 8 </th>
                        <th>Jumlah Finger/Hand Class 10 </th>
                        <th>Jumlah Finger/Hand Class Akhir </th>
                        <th>Catatan</th>
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
                url: '{{ route('phhtReport.index') }}',
                data: function (d) {
                    d.date_aw = $('input[name=date_aw]').val();
                    d.date_ak = $('input[name=date_ak]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'name', name: 'name'},
                {data: 'namaPekerja', name: 'namaPekerja'},
                {data: 'HandClass', name: 'HandClass'}, 
                {data: 'CalHandClass2', name: 'CalHandClass2'}, 
                {data: 'CalHandClass4', name: 'CalHandClass4'}, 
                {data: 'CalHandClass6', name: 'CalHandClass6'}, 
                {data: 'CalHandClass8', name: 'CalHandClass8'}, 
                {data: 'CalHandClass10', name: 'CalHandClass10'}, 
                {data: 'CalHandClassAkhir', name: 'CalHandClassAkhir'}, 
                {data: 'FingerLen2', name: 'FingerLen2'}, 
                {data: 'FingerLen4', name: 'FingerLen4'}, 
                {data: 'FingerLen6', name: 'FingerLen6'}, 
                {data: 'FingerLen8', name: 'FingerLen8'}, 
                {data: 'FingerLen10', name: 'FingerLen10'}, 
                {data: 'FingerLenAkhir', name: 'FingerLenAkhir'}, 
                {data: 'FingerHand2', name: 'FingerHand2'}, 
                {data: 'FingerHand4', name: 'FingerHand4'}, 
                {data: 'FingerHand6', name: 'FingerHand6'}, 
                {data: 'FingerHand8', name: 'FingerHand8'}, 
                {data: 'FingerHand10', name: 'FingerHand10'}, 
                {data: 'FingerHandAkhir', name: 'FingerHandAkhir'},
                {data: 'Notes', name: 'Notes'},
                {data: 'date', name: 'date'},
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
