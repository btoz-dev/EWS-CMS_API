@extends('layouts.cmsApp')

@section('content')
    <div class="container">
        <hr>
        <form class="form-inline">
            <div class="form-group mb-2">
                <h2>User Management</h2>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <a href = "{{route('usermgmt.create')}}" id="add" class="btn btn-primary btn-sm" tabindex="-1" role="button" aria-disabled="false">Tambah</a>
            </div>
        </form>
        <hr>
        <div class="table-responsive">
            <table class="table table-striped table-sm" id="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Nama Pekerja</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
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
                {data: 'username', name: 'username'},
                {data: 'email', name: 'email'},
                {data: 'namaPekerja', name: 'namaPekerja'},
                {data: 'namaRole', name: 'namaRole'},
                {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
            ]
        });

        // $('#add').on('click', function(e) {
        //     $('#modal').modal('show');
        // })
    </script>
@endsection