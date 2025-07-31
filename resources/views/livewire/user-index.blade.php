<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0">Users</h3>
        <button wire:click="create" class="btn btn-primary">Add User</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter/Search Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search by name or email...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="">All Types</option>
                        <option value="customer">Customer</option>
                        <option value="driver">Driver</option>
                        <option value="executive">Executive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending_approval">Pending</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Joined On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div>{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </td>
                            <td>{{ ucfirst($user->type) }}</td>
                            <td>
                                <span class="badge rounded-pill
                                    @if($user->status == 'active') bg-success @elseif($user->status == 'inactive') bg-danger @else bg-warning text-dark @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->status)) }}
                                </span>
                            </td>
                            {{-- THIS IS THE MODIFIED LINE --}}
                            <td>{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td>
                                <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-outline-primary">Edit</button>
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Are you sure?" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center p-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade @if($showModal) show @endif" style="display: @if($showModal) block @else none @endif;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $userId ? 'Edit User' : 'Create User' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type</label>
                                <select wire:model="type" class="form-select @error('type') is-invalid @enderror" id="type">
                                    <option value="customer">Customer</option>
                                    <option value="driver">Driver</option>
                                    <option value="executive">Executive</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror" id="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending_approval">Pending Approval</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Leave blank to keep unchanged">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input wire:model="password_confirmation" type="password" class="form-control" id="password_confirmation">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Backdrop -->
    @if($showModal)
        <div class="modal-backdrop fade show"></div>
    @endif
</div>