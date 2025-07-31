<div>
    <h3 class="mb-4">Settings</h3>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($activeTab == 'company_info') active @endif" wire:click="switchTab('company_info')">Company Info</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($activeTab == 'fare_logic') active @endif" wire:click="switchTab('fare_logic')">Fare Logic</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($activeTab == 'policies') active @endif" wire:click="switchTab('policies')">Policies</button>
                </li>
            </ul>

            <form wire:submit.prevent="save" class="mt-4">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade @if($activeTab == 'company_info') show active @endif" role="tabpanel">
                        <h5>Company Information</h5>
                        <hr>
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" wire:model="company_name" class="form-control" id="company_name">
                        </div>
                        <div class="mb-3">
                            <label for="company_email" class="form-label">Company Email</label>
                            <input type="email" wire:model="company_email" class="form-control" id="company_email">
                        </div>
                        <div class="mb-3">
                            <label for="company_address" class="form-label">Company Address</label>
                            <textarea wire:model="company_address" class="form-control" id="company_address" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="tab-pane fade @if($activeTab == 'fare_logic') show active @endif" role="tabpanel">
                         <h5>Fare & Pricing</h5>
                        <hr>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="base_fare" class="form-label">Base Fare ($)</label>
                                <input type="number" step="0.01" wire:model="base_fare" class="form-control" id="base_fare">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fare_per_km" class="form-label">Fare Per Kilometer ($)</label>
                                <input type="number" step="0.01" wire:model="fare_per_km" class="form-control" id="fare_per_km">
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade @if($activeTab == 'policies') show active @endif" role="tabpanel">
                         <h5>Policies</h5>
                        <hr>
                        <div class="mb-3">
                            <label for="cancellation_policy" class="form-label">Cancellation Policy</label>
                            <textarea wire:model="cancellation_policy" class="form-control" id="cancellation_policy" rows="5" placeholder="Enter details about cancellation fees and timeframes..."></textarea>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                         <span wire:loading.remove wire:target="save">Save Settings</span>
                         <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>