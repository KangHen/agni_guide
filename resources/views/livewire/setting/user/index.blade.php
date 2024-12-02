<?php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;

new class extends Component {
    use WithPagination;

    public string|null $search = null;
    public int $id = 0;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public int $role = 3;
    public bool $is_active = false;

    public array $paginates = [10, 25, 50];
    public int $paginate = 10;
    public array $roles;

    public function with(): array
    {
        return [
            'items' => User::query()
                ->when($this->search <> '', fn($q) => $q->where('email', 'like', '%'.$this->search.'%'))
                ->paginate($this->paginate)
        ];
    }

    public function mount()
    {
        $this->roles = [
            0 => 'Developer',
            1 => 'Admin',
            2 => 'Member',
            3 => 'User',
        ];
    }
}
?>

<div>
    <div class="flex justify-between items-center">
        <div class="flex-1">
            <input type="text" class="input input-bordered w-96" placeholder="Search...">
        </div>
        <div class="ml-2">
            <x-create-button>
                {{ __('Buat User') }}
            </x-create-button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-md">
            <!-- head -->
            <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Email</th>
                <th>Aktif</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $roles[$item->role_id] }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->is_active ? 'Ya' : 'Tidak' }}</td>
                    <td>
                        <x-edit-button />
                        <x-delete-button />
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center">No data available</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
