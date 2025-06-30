@extends('backend.layout.admin')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Dashboard</h4>

            @if ($showSubscriptionFeatures)
                <div class="row">
                    {{-- Dashboard Cards --}}

                    @if (Auth::user()->role != 1)
                        <div class="col-md-3">
                            <div class="card card-stats card-warning">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5 text-center">
                                            <i class="la la-users icon-big"></i>
                                        </div>
                                        <div class="col-7 d-flex align-items-center">
                                            <div class="numbers">
                                                <p class="card-category">Customers</p>
                                                <h4 class="card-title">{{ $total_customers ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (Auth::user()->role != 1)
                        <div class="col-md-3">
                            <a href="/admin/products">
                                <div class="card card-stats card-success">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-5 text-center">
                                                <i class="la la-bar-chart icon-big"></i>
                                            </div>
                                            <div class="col-7 d-flex align-items-center">
                                                <div class="numbers">
                                                    <p class="card-category">Products</p>
                                                    <h4 class="card-title">{{ $total_products ?? 0 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                    @if (Auth::user()->role != 1)
                        <div class="col-md-3">
                            <div class="card card-stats card-danger">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5 text-center">
                                            <i class="la la-newspaper-o icon-big"></i>
                                        </div>
                                        <div class="col-7 d-flex align-items-center">
                                            <div class="numbers">
                                                <p class="card-category">Inventory</p>
                                                <h4 class="card-title">{{ $total_inventories ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (Auth::user()->role != 1)
                        <div class="col-md-3">
                            <div class="card card-stats card-primary">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5 text-center">
                                            <i class="la la-check-circle icon-big"></i>
                                        </div>
                                        <div class="col-7 d-flex align-items-center">
                                            <div class="numbers">
                                                <p class="card-category">Order</p>
                                                <h4 class="card-title">{{ $total_orders ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (Auth::user()->role != 1)
                        <div class="col-md-3">
                            <div class="card card-stats card-primary">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5 text-center">
                                            <i class="la la-check-circle icon-big"></i>
                                        </div>
                                        <div class="col-7 d-flex align-items-center">
                                            <div class="numbers">
                                                <p class="card-category">Users</p>
                                                <h4 class="card-title">{{ $total_users ?? 0 }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (Auth::user()->role == 1)
                        <div class="col-md-3">
                            <a href="/admin/organizations">
                                <div class="card card-stats card-primary">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-5 text-center">
                                                <i class="la la-check-circle icon-big"></i>
                                            </div>
                                            <div class="col-7 d-flex align-items-center">
                                                <div class="numbers">
                                                    <p class="card-category">Organizations</p>
                                                    <h4 class="card-title">{{ $total_organizations ?? 0 }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-primary">
                                <div class="card-body">
                                    <div class="row">
                                        <form action="{{ url('/admin/upload-apk') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <label>Upload APK:</label>
                                            <input type="file" name="apk_file" accept=".apk" required>
                                            <button type="submit">Upload</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>


                @if (Auth::user()->role != 1)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Subscription Features</h5>
                                    <table class="table table-bordered" id="featuresTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>

                                                <th>Feature Name</th>
                                                <th>Feature Description</th>
                                                <th>Feature Price</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                @if (Auth::user()->role != 1)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning text-center">
                                <h5>You don't have an active subscription.</h5>
                                <p>Please subscribe to unlock all features.</p>
                                <a href="/purchaseSubscription" class="btn btn-primary">
                                    Purchase
                                </a>
                            </div>
                        </div>
                    </div>

                @endif
            @endif

        </div>
    </div>
@endsection

@section('custom_javascript')
    <script>
        $(document).ready(function () {
            $('#featuresTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("dashboard") }}',
                rowGroup: {
                    dataSrc: 'name'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },

                    { data: 'title', name: 'subs_features_details.title' },
                    { data: 'detail_description', name: 'subs_features_details.description' },
                    { data: 'detail_price', name: 'subs_features_details.price' }
                ]

            });
        });
    </script>
@endsection