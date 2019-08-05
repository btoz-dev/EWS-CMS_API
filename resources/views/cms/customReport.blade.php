@extends('layouts.cmsApp')

@section('stylesheet')
<link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
@endsection

@section('content')
    <div class="container">
        <hr>
        <h2>Reports</h2>
        <form class="form-inline">
            <div class="form-group mb-2">
                Tanggal &nbsp; <input class="form-control" type="date" id="dateAwal" value="{{$now}}">
            </div>
            <div class="form-group mx-sm-2 mb-2">
                Aktifitas &nbsp; <select class="selectpicker" id="select-aktifitas"></select>
            </div>
            <div class="form-group mb-2">
                <div class="spinner-border text-warning" role="status" style="display:none;" id="loading-export">
                    <span class="sr-only">Loading...</span>
                </div>
                    <!-- <button type="button" class="form-control mx-sm-3 btn btn-success" name="export">Export</button> -->

            </div>
        </form>

        <hr>

        <div class="row">
            <div class="col-12">
                <b>Blok</b>
                <div class="table-responsive">
                    <table class="table table-striped table-sm" id="data-table-blok">
                        <thead>
                            <tr>
                                <th>Kode Blok</th>
                                <th class="count_this">Total Pokok</th>
                                <th class="count_this">Realisasi Pokok</th>
                                <th class="count_this">Sisa Pokok</th>
                                <th >% Realisasi</th>
                                <th >#</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Grand Total</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0</th>
                                <th>0 %</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- <div class="col-4">
                <canvas id="myChart2" style="display: none;"></canvas>
            </div> -->
        </div>

        <hr>

        <div class="row">
            <div class="col-12">
                <b>Detil Pokok</b>
                <div class="table-responsive">
                    <table class="table table-striped table-sm" id="data-table-pokok">
                        <thead>
                            <tr>
                                <th>Kode Pokok</th>
                                <th>Tanggal RKH</th>
                                <th>Aktifitas</th>
                                <th>Nama Mandor</th>
                                <th>Tanggal Realisasi</th>
                                <th>Nama SPI</th>
                                <th>Catatan SPI</th>
                                <th>Tanggal Follow Up</th>
                                <th>Nama Kawil</th>
                                <th>Catatan Kawil</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
    <script type="text/javascript">
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        function numberWithoutCommas(x) {
            return x.replace(/\,/g,'');
        }

        // Options header for post 
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

        // set selectpicker bootstrap version
        $.fn.selectpicker.Constructor.BootstrapVersion = '4';

        // set option selectpicker
        $('#select-aktifitas').selectpicker({
            liveSearch : true
        });

        // get data first time today when page load
        $.post('{{route('filterByDate')}}',{date: "{{ $now }}"}, function (e) {
            // console.log(e);
            $('#select-aktifitas').html(e);
            $('#select-aktifitas').selectpicker('refresh');
        })

        // init data table for detil blok table
        $.extend( $.fn.dataTable.defaults, {
            dom: "<'row'<'col'l><'col'B><'col'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    text: 'Export',
                    attr: {
                        name: 'export',
                        class: 'form-control mx-sm-3 btn btn-success'
                    },
                    action: function ( e, dt, node, config ) {
                        alert( 'Button Export' );
                    }
                }
            ],
        } );

        // initBlok($('#dateAwal').val(),$('#select-aktifitas').val());

        var Table1 = $('#data-table-blok').DataTable();
        var Table2 = $('#data-table-pokok').DataTable();

        // ambil data aktifitas setiap tanggal berubah
        $('#dateAwal').on('change', function(e) {
            Table1.clear();
            $('#data-table-blok tfoot').html('<tr><th>Grand Total</th><th>0</th><th>0</th><th>0</th><th>0 %</th></tr>');
            Table1.draw();
            Table2.clear();
            Table2.draw();
            $('#select-aktifitas').val('');
            // console.log($('#select-aktifitas').val());
            $('#select-aktifitas').selectpicker('refresh');
            $.post('{{route('filterByDate')}}',{date: $(this).val()}, function (e) {
                $('#select-aktifitas').html(e);
                $('#select-aktifitas').selectpicker('refresh');
            })
            // initBlok($('#dateAwal').val(),$('#select-aktifitas').val());
        })

        // ambil data blok ketika aktifitas berubah
        $('#select-aktifitas').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            Table2.clear();
            Table2.draw();
            // console.log($(this).val());
            // re-init data table for detil blok table
            initBlok($('#dateAwal').val(),$('#select-aktifitas').val());
            e.preventDefault();
        })

        var headingsTable1 = [];
        $(Table1.table().header()).find('th').each(function () {
          // access each tr's tds from here using $(this)
          var head = $(this).text()
          headingsTable1.push(head);
        });

        var headingsTable2 = [];
        $(Table2.table().header()).find('th').each(function () {
          // access each tr's tds from here using $(this)
          var head = $(this).text()
          headingsTable2.push(head);
        });

        function initBlok(tanggal, codeJob) {
            var Table1 = $('#data-table-blok').DataTable({
                destroy: true,
                // dom: "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                dom: "<'row'<'col'l><'col'B><'col'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        text: 'Export',
                        attr: {
                            name: 'export',
                            class: 'form-control mx-sm-3 btn btn-success',
                            
                        },
                        data: [tanggal, codeJob],
                        action: function ( e, dt, node, config ) {
                            var url = '{{route('exportCustom')}}';
                            var params = { 
                                    heading: headingsTable1, 
                                    job: "BLOK",
                                    data: config.data
                                };
                            $.ajax({
                                type: "POST",
                                url: url,
                                data: params,
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                success: function(response, status, request) {
                                    // console.log(response);
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
                        }
                    }
                ],
                processing: true,
                // serverSide: true, //tidak menggunakan server side agar footer callback memberikan sum seluruh data
                ajax: {
                    url: '{{ route('getDetilBlok') }}',
                    data: function (d) {
                        d.date = tanggal;
                        d.aktifitas = codeJob;
                    }
                },
                columns: [
                    {data: 'codeBlok', name: 'codeBlok'},
                    {data: 'totalPokok', name: 'totalPokok', render: function (a) { return numberWithCommas(a) }},
                    {data: 'pokokDone', name: 'pokokDone', render: function (a) { return numberWithCommas(a) }},
                    {data: 'pokokNDone', name: 'pokokNDone', render: function (a) { return numberWithCommas(a) }},
                    {data: 'persentase', name: 'persentase'},
                    {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
                ],
                "footerCallback": function( tfoot, data, start, end, display ) {
                    // $(tfoot).find('th').eq(0).html( "Starting index is "+start );
                    var api = this.api();
                    api.columns('.count_this').every(function() {
                        var sum = this
                            .data()
                            .reduce(function(a, b) {
                            var x = parseFloat(a) || 0;
                            var y = parseFloat(b) || 0;
                            return x + y;
                            }, 0);
                        // console.log(sum); //alert(sum);
                        $(this.footer()).html(numberWithCommas(sum));
                    });
                    $(api.columns(4).footer()).html(function() {
                        var total = parseFloat(numberWithoutCommas($(tfoot).find('th').eq(1).text()));
                        var realisasi = parseFloat(numberWithoutCommas($(tfoot).find('th').eq(2).text()));
                        var persentase = parseFloat(realisasi * 100 / total).toFixed(2);
                        return persentase + ' %';
                    });
                }
            });
        }

        $(document).ready(function () {
            $(document).on("click", "button#showDetail", function (event) {
                event.preventDefault();
                // console.log($(this).data());
                // get data detil pokok
                var data_date = $(this).data('date'),
                    data_aktifitas = $(this).data('aktifitas'),
                    data_parent = $(this).data('parent'),
                    data_blok = $(this).data('blok'),
                    data_rkh = $(this).data('rkh');
                    data_id = $(this).data('id');
                if (data_parent == '003') {
                    var columns2 = [
                        {data: 'codeTanaman', name: 'codeTanaman'},
                        {data: 'rkhDate', name: 'rkhDate'},
                        {data: 'aktifitas', name: 'aktifitas'},
                        {data: 'mandor', name: 'mandor'},
                        {data: 'realisationDate', name: 'realisationDate'},
                        {data: 'NamaMandor', name: 'NamaMandor', title: 'Nama Mandor'},
                        {data: 'mandorNote', name: 'mandorNote', title: 'Catatan Mandor'},
                        {data: 'kawilDate', name: 'kawilDate'},
                        {data: 'NamaKawil', name: 'NamaKawil'},
                        {data: 'kawilNote', name: 'kawilNote'}
                    ];
                }else {
                    var columns2 = [
                        {data: 'codeTanaman', name: 'codeTanaman'},
                        {data: 'rkhDate', name: 'rkhDate'},
                        {data: 'aktifitas', name: 'aktifitas'},
                        {data: 'mandor', name: 'mandor'},
                        {data: 'realisationDate', name: 'realisationDate'},
                        {data: 'NamaSPI', name: 'NamaSPI'},
                        {data: 'spiNote', name: 'spiNote'},
                        {data: 'kawilDate', name: 'kawilDate'},
                        {data: 'NamaKawil', name: 'NamaKawil'},
                        {data: 'kawilNote', name: 'kawilNote'}
                    ];
                }
                var Table2 = $('#data-table-pokok').DataTable({
                    destroy: true,
                    dom: "<'row'<'col'l><'col'B><'col'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        {
                            text: 'Export',
                            attr: {
                                name: 'export',
                                class: 'form-control mx-sm-3 btn btn-success',
                                
                            },
                            data: [data_date, data_aktifitas, data_parent, data_blok, data_rkh, data_id],
                            action: function ( e, dt, node, config ) {
                                var url = '{{route('exportCustom')}}';
                                var params = { 
                                        heading: headingsTable2, 
                                        job: "DETIL",
                                        data: config.data
                                    };
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: params,
                                    xhrFields: {
                                        responseType: 'blob'
                                    },
                                    success: function(response, status, request) {
                                        // console.log(response);
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
                            }
                        }
                    ],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('getDetilPokok') }}',
                        data: function (d) {
                            console.log(d);
                            d.date = data_date,
                            d.aktifitas = data_aktifitas,
                            d.parent = data_parent,
                            d.blok = data_blok,
                            d.rkh = data_rkh,
                            d.id = data_id
                        }
                    },
                    columns: columns2,
                });
            });
        });
    </script>
@endsection
