@extends('backend.layout.admin')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Dashboard Organizations</h4>
        </div>

        <div class="container">
            <h2>Organization List</h2>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered" id="organization-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Number</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Created At</th>
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
            const table = $('#organization-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.listorganizations') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    {
                        data: 'org_name',
                        name: 'org_name',
                        orderable: false,
                        render: function (data, type, row) {
                            return `<a href='/admin/dashboard/org_detail/${row.id}'>${row.org_name}</a>`;
                        }
                    },
                    { data: 'org_email', name: 'org_email' },
                    { data: 'org_number', name: 'org_number' },
                    { data: 'org_address', name: 'org_address' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
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
                        url: `/admin/dashboard/organizations/toggleStatus/${orgId}/${newStatus}`,
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