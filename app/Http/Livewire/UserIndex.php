<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UserIndex extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    // Filters
    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    
    // Modal properties
    public $showModal = false;
    public $userId;
    public $name, $email, $password, $password_confirmation, $type, $status;

    public function rules() {
        $rules = [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'type' => 'required|in:customer,driver,executive',
            'status' => 'required|in:active,inactive,pending_approval',
        ];

        // Password is only required when creating a new user, not when editing.
        if (!$this->userId) {
            $rules['password'] = 'required|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|min:8|confirmed';
        }

        return $rules;
    }

    // Reset page on filter update
    public function updatingSearch() { $this->resetPage(); }
    public function updatingTypeFilter() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function create()
    {
        $this->resetInput();
        $this->showModal = true;
    }

    public function edit(User $user)
    {
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->type = $user->type;
        $this->status = $user->status;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        $userData = [
            'name'   => $validatedData['name'],
            'email'  => $validatedData['email'],
            'type'   => $validatedData['type'],
            'status' => $validatedData['status'],
        ];

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($userData);
            session()->flash('message', 'User successfully updated.');
        } else {
            User::create($userData);
            session()->flash('message', 'User successfully created.');
        }

        // This line will now work correctly because the method below exists.
        $this->closeModal();
    }

    /**
     * THIS IS THE NEW METHOD THAT WAS MISSING.
     * It hides the modal and resets the form fields.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInput();
    }

    public function delete(User $user)
    {
        $user->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    private function resetInput()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->type = 'customer';
        $this->status = 'active';
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.user-index', compact('users'));
    }
}