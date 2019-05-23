@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <form class="form-inline">
            <div class="form-group mb-2">
                <h2>User Management</h2>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                @can('add_usermgmt')
                    <a href = "{{route('usermgmt.create')}}" id="add" class="btn btn-primary btn-sm" tabindex="-1" role="button" aria-disabled="false">Tambah</a>
                @endcan
            </div>
            @if (session('alert'))
                <div class="alert alert-info alert-dismissible" role="alert">
                    {{ session('alert') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </form>
        <hr>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="showDetailModal" tabindex="-1" role="dialog" aria-labelledby="showDetailLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showDetailLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="hapusModal" tabindex="-1" role="dialog" aria-labelledby="showDetailLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showDetailLabel">Hapus User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Anda yakin ingin menghapus ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-secondary" onclick="event.preventDefault(); document.getElementById('destroy-form').submit();">Hapus</button>

                    <form id='destroy-form' action="" method='POST' style='display: none;'>
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('usermgmt.index') }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'username', name: 'username'},
                {data: 'role', name: 'role'},
                {data: 'created', name: 'created'},
                {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
            ]
        });

        $(document).ready(function () {
            $(document).on("click", "button#showDetail", function (event) {
                event.preventDefault();

                $.get('{{route('usermgmt.show', [''])}}/'+$(this).data('id'), function (e) {
                    // body...
                    $('.modal-content').html(e);
                    $('#showDetailModal').modal('show');
                })
            });

            $(document).on("click", "button#penghapusan", function (event) {
                $('#hapusModal').find('form').attr('action', $(this).data('url'));
                event.preventDefault();
            });
        });
    </script>
@endsection