<?php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

new class extends Component {
    use WithPagination;

    public string|null $search = null;
    public int $id = 0;
    public int $no = 1;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string|null $phone = '';
    public string|null $address = '';
    public string|null $city = '';
    public int $role_id = 3;
    public int $is_active = 1;

    public array $paginates = [10, 25, 50];
    public int $paginate = 10;
    public array $roles;
    public array $all_roles;
    public int $filter_role_id = 0;
    public array $status = [0 => 'Inactive', 1 => 'Active'];

    /**
     * @return array
     */
    public function with(): array
    {
        return [
            'items' => User::query()
                ->when($this->search <> null, fn($q) => $q->where('email', 'like', '%'.$this->search.'%'))
                ->when($this->filter_role_id != 0, fn($q) => $q->where('role_id', $this->filter_role_id))
                ->paginate($this->paginate)
        ];
    }

    /**
     * Mount Setting
     * @return void
     */
    public function mount(): void
    {
        $this->roles = [
            -1 => 'Developer',
            1 => 'Admin',
            2 => 'Member',
            3 => 'User',
        ];

        $this->all_roles = [
            0 => 'Semua',
            -1 => 'Developer',
            1 => 'Admin',
            2 => 'Member',
            3 => 'User',
        ];

        if (request()->get('page') > 1) {
            $this->no = ((request()->get('page')-1)*$this->paginate) + 1;
        }
    }

    /**
     * Show User
     * @param int $id
     * @return void
     */
    public function show(int $id): void
    {
        $item = User::find($id);
        $this->id = $item->id;
        $this->name = $item->name;
        $this->email = $item->email;
        $this->role_id = $item->role_id;
        $this->phone = $item->phone;
        $this->address = $item->address;
        $this->city = $item->city;
        $this->is_active = $item->is_active;
    }

    /**
     * Edit User
     * @param int $id
     * @return void
     */
    public function edit(int $id): void
    {
        $this->show($id);
        $this->dispatch('open-modal', 'form');
    }

    /**
     * Update or Create Setting
     * @return void
     */
    public function saved(): void
    {
        if ($this->id) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Store User
     * @return void
     */
    public function store(): void
    {
        $data = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'not_in:' . auth()->user()->email],
            'password' => ['required', 'string', Password::defaults()],
        ];

        $validated = $this->validate($data);

       /* $name = NULL;
        if($this->avatar){
            $name = date('Ymd').'_'.uniqid().'.'.$this->avatar->extension();
            $this->avatar->storeAs('avatar', $name, 'custom_public_path');
        }*/

        $saved = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => now()->format('Y-m-d H:i:s'),
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'is_active' => 1,
            //'avatar' => $name,
        ]);

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Saved Successfully');
        } else {
            session()->flash('error', 'Error Created');
        }

        $this->redirect('/user', navigate: true);
    }

    /**
     * Update User
     * @return void
     */
    public function update(): void
    {
        $data = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'not_in:' . auth()->user()->email],
        ];

        if($this->password){
            $data['password'] = ['required', 'string', Password::defaults()];
        }

        $validated = $this->validate($data);

        $user = User::find($this->id);
        //update password
        $password = $user->password;
        if($this->password){
            $password = Hash::make($this->password);
        }

        /*$name = $this->oldAvatar;
        if($this->avatar){
            $image = public_path('uploads/avatar/'. $name);
            if(File::exists($image)){
                File::delete($image);
            }
            //save avatar new
            $name = date('Ymd').'_'.uniqid().'.'.$this->avatar->extension();
            $this->avatar->storeAs('avatar', $name, 'custom_public_path');
        }*/

        $saved = User::where('id', $this->id)
            ->update([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $password,
                'role_id' => $this->role_id,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'is_active' => 1,
                //'avatar' => $name,
            ]);

        if ($saved) {
            $this->_reset();
            session()->flash('message', 'Updated Successfully');
        } else {
            session()->flash('error', 'Error Updated');
        }

        $this->redirect('/user', navigate: true);
    }

    /**
     * Set Active User
     * @param int $id
     * @param int $value
     * @return void
     */
    public  function  setActive(int $id, int $value): void
    {
        $user = User::find($id);
        $user->is_active = $value;

        if ($user->save()) {
            session()->flash('message', 'Updated Successfully');
        } else {
            session()->flash('error', 'Error Updated');
        }

        $this->redirect('/user', navigate: true);
    }

    /**
     * Delete User
     * @return void
     */
    public function delete(): void
    {
        $deleted = User::find($this->id)->delete();
        $this->id = 0;

        if ($deleted) {
            session()->flash('message', 'Deleted Successfully');
        } else {
            session()->flash('error', 'Error Deleted');
        }

        $this->redirect('/user', navigate: true);
    }

    /**
     * Filtered
     * @return void
     */
    public  function filtered(): void
    {
        $this->resetPage();
    }

    /**
     * Reset
     * @return void
     */
    protected function _reset(): void
    {
        $this->reset('id', 'name', 'email', 'password', 'role_id', 'phone', 'address', 'city');
    }
}
?>

<div>
    <x-loading />

    <div class="flex justify-between items-center">
        <div class="flex-1">
            <x-text-input wire:model="search" wire:input.debounce.300ms="filtered()" class="w-96" placeholder="Search..."></x-text-input>
        </div>
        <div class="flex-1">
            <x-select class="w-72" :data="$all_roles" wire:model="filter_role_id" wire:change="filtered()"></x-select>
        </div>
        <div class="ml-2">
            <x-create-button x-on:click="$dispatch('open-modal', 'form')">
                {{ __('Buat User') }}
            </x-create-button>
        </div>
    </div>

    <div class="overflow-x-auto mt-3">
        <table class="table table-md">
            <!-- head -->
            <thead>
            <tr>
                <th class="w-12">No</th>
                <th>Nama</th>
                <th>Role</th>
                <th>Email</th>
                <th>Aktif</th>
                <th class="w-40">#</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $roles[$item->role_id] }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        <x-select :data="$status" :value="$item->is_active" wire:change="setActive({{ $item->id }}, $event.target.value)" />
                    </td>
                    <td>
                        <x-edit-button wire:click="edit({{ $item->id }})" />
                        <x-delete-button x-on:click.prevent="$dispatch('confirm-delete', {{ $item->id }})" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No data available</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="w-full mt-3">
            {{ $items->links() }}
        </div>
    </div>

    @include('livewire.setting.user.form')

    <x-modal name="confirm-user-deleted" :show="$errors->isNotEmpty()" maxWidth="sm">
        <div class="p-5">
            <h3 class="text-lg font-bold">Hapus</h3>
            <p class="py-4">Yakin hapus data ini?</p>
            <div class="modal-action">
                <x-secondary-button x-on:click="$dispatch('cancel-delete')">
                    {{ __('Batalkan') }}
                </x-secondary-button>

                <x-danger-button wire:click="delete" class="ms-3">
                    {{ __('Hapus') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal>

    @script
    <script>
        $wire.on('confirm-delete', (id) => {
            $wire.set('id', id);
            $wire.dispatch('open-modal', 'confirm-user-deleted');
        });

        $wire.on('cancel-delete', () => {
            $wire.set('id', 0);
            $wire.dispatch('close-modal', 'confirm-user-deleted');
        });
    </script>
    @endscript
</div>
