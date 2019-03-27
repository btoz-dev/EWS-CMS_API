@extends('layouts.cmsApp')

@section('stylesheet')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.7/dist/css/bootstrap-select.min.css">
@endsection

@section('content')
    <div class="container">
        <hr>
        <h2>Reports</h2>
        <form class="form-inline">
            <div class="form-group mb-2">
                <input class="form-control" type="date" id="dateAwal">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <i class="fas fa-long-arrow-alt-right"></i>
            </div>
            <div class="form-group  mb-2">
                <input class="form-control" type="date" id="dateAkhir">
            </div>
        </form>
        <select class="selectpicker" id="select-rkh">
            
        </select>
        <select class="selectpicker" id="select-aktifitas"></select>
        <select class="selectpicker" id="select-codeBlok"></select>
        <button type="button" id="filter" class="btn btn-primary">Filter</button>
        <hr>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>Kode Tanaman</th>
                        <th>Status</th>
                        <th>Tanggal RKH</th>
                        <th>Tanggal Realisasi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.7/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.fn.selectpicker.Constructor.BootstrapVersion = '4';

        $('#select-rkh, #select-aktifitas, #select-codeBlok').selectpicker({
            liveSearch : true
        });

        $('#dateAwal').on('change', function(e) {
            console.log($(this).val());
            console.log($('#dateAkhir').val());
            if ($(this).val() > $('#dateAkhir').val()) {
                $('#dateAkhir').val($(this).val());
            }

            $.post('{{route('postDropdown')}}',{type: 'rkh', dateAwal: $(this).val(), dateAkhir: $('#dateAkhir').val()}, function (e) {
                // body...
                $('#select-rkh').html(e);
                $('#select-rkh').selectpicker('refresh');
            })
        })

        $('#dateAkhir').on('change', function(e) {
            console.log($(this).val());
            if ($(this).val() < $('#dateAwal').val()) {
                $('#dateAwal').val($(this).val());
            }

            $.post('{{route('postDropdown')}}',{type: 'rkh', dateAwal: $('#dateAwal').val(), dateAkhir: $(this).val()}, function (e) {
                // body...
                $('#select-rkh').html(e);
                $('#select-rkh').selectpicker('refresh');
            })
        })

        $('#select-rkh').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            // body...
            $.post('{{route('postDropdown')}}',{type: 'aktifitas', id: $('#select-rkh').val()}, function (e) {
                // body...
                $('#select-aktifitas').html(e);
                $('#select-aktifitas').selectpicker('refresh');
            })
        })

        $('#select-aktifitas').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            // body...
            $.post('{{route('postDropdown')}}',{type: 'blok', id: $('#select-aktifitas').val()}, function (e) {
                // body...
                $('#select-codeBlok').html(e);
                $('#select-codeBlok').selectpicker('refresh');
            })
        })

        var Table = $('#data-table').DataTable({
            data: [],
            processing: true,
            // serverSide: true,
            // ajax: '{{ route('customReport.index') }}',
            columns: [
                {data: 'codeTanaman', name: 'codeTanaman'},
                {data: 'status', name: 'status'},
                {data: 'rkhDate', name: 'rkhDate'},
                {data: 'created_at', name: 'created_at'}
            ],
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                // console.log(nRow);
                // console.log(aData);
                // console.log(iDisplayIndex);
                // console.log(iDisplayIndexFull);
                if ( aData['status'] == 0 )
                {
                    $(nRow).addClass( 'table-danger' );
                }
                if ( aData['status'] == 1 )
                {
                    $(nRow).addClass( 'table-success' );
                }
            }
        });

        $('#filter').on('click', function(e) {
            // console.log($('#select-rkh').val());
            // console.log($('#select-aktifitas').val());
            // console.log($('#select-codeBlok').val());

            $.post('{{route('postFilter')}}',{rkhCode: $('#select-rkh').val(), codeAlojob: $('#select-aktifitas').val(), codeBlok: $('#select-codeBlok').val()}, function (e) {
                // body...
                Table.clear().draw();
                Table.rows.add( e ).draw();
            })
        })

        
    </script>
@endsection