<?php

use Livewire\Component;
use App\Models\Nurse;
use Illuminate\Support\Carbon;

new class extends Component {
    public ?Nurse $nurse = null;

    public function mount(?string $nurseId = null): void
    {
        if ($nurseId) {
            if (!is_numeric($nurseId)) {
                $this->redirectWithError($nurseId);
                return;
            }

            $nurse = Nurse::withTrashed()->find((int)$nurseId);

            if (!$nurse) {
                $this->redirectWithError($nurseId);
                return;
            }

            $nurse->load(['user' => fn($q) => $q->withTrashed()->with('roles'), 'updatedBy']);

            $this->nurse = $nurse;
        }
    }

    protected function redirectWithError($nurseId)
    {
        Session::flash('error', __('nurse.errors.not_found', ['id' => $nurseId]));
        $this->redirectRoute('nurse.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('nurse.detail', ['id' => $this->nurse->id, 'name' => ucwords(strtolower($this->nurse->names))
                . ' ' .
                ucwords(strtolower($this->nurse->paternal_surname))])])
            ->title(__('views.nurse.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($nurse && $nurse->trashed())
            <x-shared.alert type="info">{{ __('nurse.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ __('common.dni') }}</flux:label>
                <flux:input readonly value="{{ $nurse->dni }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $nurse->names }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.paternal_surname') }}</flux:label>
                <flux:input readonly value="{{ $nurse->paternal_surname }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.maternal_surname') }}</flux:label>
                <flux:input readonly value="{{ $nurse->maternal_surname ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.hired_at') }}</flux:label>
                <flux:input readonly
                            value="{{ Carbon::createFromFormat('Y-m-d',$nurse->hired_at)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null') }}"
                            type="text"/>
            </flux:field>
            <flux:input readonly value="{{ $nurse->user?->email ?? __('common.null') }}"
                        label="{{ __('common.email') }}" type="email"/>
            <flux:input readonly value="{{ $nurse->phone ?? __('common.null') }}" label="{{ __('common.phone') }}"
                        type="text"/>
            <flux:input readonly value="{{ $nurse->address ?? __('common.null') }}" label="{{ __('common.address') }}"
                        type="text"/>
            <flux:field>
                <flux:label>{{ trans_choice('auth.user',1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $nurse->user?->username ?? __('common.null') }}"
                                type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="{{route('user.detail',['userId' => $nurse->user?->id])}}">
                    </flux:button>
                </flux:input.group>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('role.role',1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $nurse->user?->roles->first()?->name ?? __('common.null') }}"
                                type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="#">
                    </flux:button>
                </flux:input.group>
            </flux:field>
            <div class="col-span-full">
                <flux:input readonly value="{{ $nurse->updatedBy->username ?? __('common.inserted_by_null') }}"
                            label="{{ __('common.updated_by') }}" type="text"/>
            </div>
            <flux:input readonly
                        value="{{ $nurse->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $nurse->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if(!auth()->user()->is($nurse->user))
                        @canany(['sys.admin', 'nurse.update', 'nurse.delete', 'nurse.restore'])
                            <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto"
                                         wire:navigate
                                         href="{{ route('nurse.edit', ['nurseId' => $nurse->id]) }}">
                                {{ __('common.edit') }}
                            </flux:button>
                        @endcanany
                    @endif
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
