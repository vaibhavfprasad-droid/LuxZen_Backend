<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Setting;

class Settings extends Component
{
    // For managing the active tab
    public $activeTab = 'company_info';

    // Properties to hold the settings values
    public $company_name;
    public $company_email;
    public $company_address;
    public $cancellation_policy;
    public $fare_per_km;
    public $base_fare;

    // Load settings from the database when the component mounts
    public function mount()
    {
        $settings = Setting::pluck('value', 'key');

        $this->company_name = $settings['company_name'] ?? '';
        $this->company_email = $settings['company_email'] ?? '';
        $this->company_address = $settings['company_address'] ?? '';
        $this->cancellation_policy = $settings['cancellation_policy'] ?? '';
        $this->fare_per_km = $settings['fare_per_km'] ?? '0.50';
        $this->base_fare = $settings['base_fare'] ?? '5.00';
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function save()
    {
        // This is a very efficient way to save key-value settings
        $settingsData = [
            'company_name' => $this->company_name,
            'company_email' => $this->company_email,
            'company_address' => $this->company_address,
            'cancellation_policy' => $this->cancellation_policy,
            'fare_per_km' => $this->fare_per_km,
            'base_fare' => $this->base_fare,
        ];

        foreach ($settingsData as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        session()->flash('message', 'Settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.settings');
    }
}