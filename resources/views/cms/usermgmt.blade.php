@extends('layouts.cmsApp')

@section('content')
    <!-- <div class="row mt-3">
        <div class="col">
            <h2>User Management</h2>
        </div>
        <div class="col">
            <a href="{{ url('/usermgmt/create') }}" class="btn btn-primary btn-sm">Tambah User</a>
        </div>
    </div> -->
    <div class="container">
        <hr>
        <h2>User Management</h2>
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
            ]
        });
    </script>
@endsection