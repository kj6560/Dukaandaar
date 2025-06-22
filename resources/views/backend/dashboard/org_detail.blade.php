@extends('backend.layout.admin')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">{{ $organization->org_name }}</h4>
        </div>
        <div class="container mt-5">
            <div class="card shadow rounded">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= isset($organization->id) && $organization->id ? 'Edit' : 'Create' ?>
                        {{ $organization->org_name }}
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/dashboard/organization/save">
                        @csrf
                        <input type="hidden" name="id" value="<?= htmlspecialchars($organization->id ?? '') ?>">

                        <div class="mb-3">
                            <label for="org_name" class="form-label">Organization Name</label>
                            <input type="text" class="form-control" id="org_name" name="org_name" required
                                value="<?= htmlspecialchars($organization->org_name ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="org_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="org_email" name="org_email"
                                value="<?= htmlspecialchars($organization->org_email ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="org_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="org_number" name="org_number"
                                value="<?= htmlspecialchars($organization->org_number ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="org_address" class="form-label">Address</label>
                            <textarea class="form-control" id="org_address" name="org_address"
                                rows="3"><?= htmlspecialchars($organization->org_address ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Is Active?</label>
                            <select class="form-select" id="is_active" name="is_active" required>
                                <option value="1" <?= isset($organization->is_active) && $organization->is_active == 1 ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= isset($organization->is_active) && $organization->is_active == 0 ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <?= isset($organization->id) && $organization->id ? 'Update' : 'Create' ?>
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
        <div class="container mt-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= isset($editing) && $editing ? 'Edit' : 'Create' ?> Feature Purchase</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/dashboard/saveUserFeaturePurchase">
                        @csrf
                        <input type="hidden" name="id" value="<?= htmlspecialchars($purchase->id ?? '') ?>">

                        <!-- org_id -->
                        <div class="mb-3">
                            <label for="org_id" class="form-label">Organization ID</label>
                            <input type="number" class="form-control" id="org_id" name="org_id"
                                value="<?= htmlspecialchars($purchase->org_id ?? '') ?>" required>
                        </div>

                        <!-- feature_id -->
                        <div class="mb-3">
                            <label for="feature_id" class="form-label">Feature</label>
                            <select class="form-select" id="feature_id" name="feature_id" required>
                                <option value="">-- Select Feature --</option>
                                <?php foreach ($features as $feature): ?>
                                <option value="<?= $feature['id'] ?>" <?= isset($purchase->feature_id) && $purchase->feature_id == $feature['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($feature['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- transaction_id -->
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="number" class="form-control" id="transaction_id" name="transaction_id"
                                value="<?= htmlspecialchars($purchase->transaction_id ?? '') ?>" required>
                        </div>

                        <!-- purchased_at -->
                        <div class="mb-3">
                            <label for="purchased_at" class="form-label">Purchased At</label>
                            <input type="datetime-local" class="form-control" id="purchased_at" name="purchased_at"
                                value="<?= isset($purchase->purchased_at) ? date('Y-m-d\TH:i', strtotime($purchase->purchased_at)) : '' ?>">
                        </div>

                        <!-- expires_at -->
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expires At</label>
                            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at"
                                value="<?= isset($purchase->expires_at) ? date('Y-m-d\TH:i', strtotime($purchase->expires_at)) : '' ?>">
                        </div>

                        <!-- expired -->
                        <div class="mb-3">
                            <label for="expired" class="form-label">Expired?</label>
                            <select class="form-select" id="expired" name="expired" required>
                                <option value="0" <?= isset($purchase->expired) && $purchase->expired == 0 ? 'selected' : '' ?>>No</option>
                                <option value="1" <?= isset($purchase->expired) && $purchase->expired == 1 ? 'selected' : '' ?>>Yes</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="btn btn-success"><?= isset($editing) && $editing ? 'Update' : 'Submit' ?></button>
                        
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection