@extends('backend.layout.admin')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Users</h4>
        </div>

        <div class="container">
            <h2>Organization List</h2>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered" id="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>organization</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('custom_javascript')
    <script type="text/javascript">
        $(function () {
            const table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.listusers') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        render: function (data, type, row) {
                            return `<a href='/admin/dashboard/user_detail/${row.id}'>${row.name}</a>`;
                        }
                    },
                    { data: 'email', name: 'email' },
                    { data: 'org_name', name: 'org_name' },
                    {
                        data: 'is_active', name: 'is_active',
                        render: function (data, type, row) {
                            return row.is_active ?
                                `<span class="badge badge-success">Active</span>` :
                                `<span class="badge badge-danger">Inactive</span>`;
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            if (row.is_active) {
                                return `<button class="btn btn-sm btn-danger toggle-status" data-id="${row.id}" data-status="0">Disable</button>`;
                            } else {
                                return `<button class="btn btn-sm btn-success toggle-status" data-id="${row.id}" data-status="1">Enable</button>`;
                            }
                        }
                    }
                ]
            });

            // Enable/Disable toggle
            $(document).on('click', '.toggle-status', function () {
                const orgId = $(this).data('id');
                const newStatus = $(this).data('status');
                const actionText = newStatus === 1 ? 'enable' : 'disable';

                if (confirm(`Are you sure you want to ${actionText} this organization?`)) {
                    $.ajax({
                        url: `/admin/dashboard/users/toggleStatus/${orgId}/${newStatus}`,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            is_active: newStatus
                        },
                        success: function () {
                            table.ajax.reload();
                        },
                        error: function () {
                            alert('Operation failed.');
                        }
                    });
                }
            });
        });
    </script>
@endsection