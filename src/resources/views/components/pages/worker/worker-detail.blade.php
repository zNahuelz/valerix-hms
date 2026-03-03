<?php

use Livewire\Component;
use App\Models\Worker;
use Illuminate\Support\Carbon;

new class extends Component
{
    public ?Worker $worker = null;

    public function mount(?string $workerId = null): void
    {
        if ($workerId) {
            if (!is_numeric($workerId)) {
                $this->redirectWithError($workerId);
                return;
            }

            $worker = Worker::withTrashed()->find((int)$workerId);

            if (!$worker) {
                $this->redirectWithError($workerId);
                return;
            }

            $worker->load(['user' => fn($q) => $q->withTrashed()->with('roles'),'updatedBy']);

            $this->worker = $worker;
        }
    }

    protected function redirectWithError($workerId)
    {
        Session::flash('error', __('worker.errors.not_found', ['id' => $workerId]));
        $this->redirectRoute('worker.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('worker.detail', ['id' => $this->worker->id, 'name' => ucwords(strtolower($this->worker->names))
                .' '.
                ucwords(strtolower($this->worker->paternal_surname))])])
            ->title(__('views.worker.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($worker && $worker->trashed())
            <x-shared.alert type="info">{{ __('worker.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ __('common.dni') }}</flux:label>
                <flux:input readonly value="{{ $worker->dni }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $worker->names }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.paternal_surname') }}</flux:label>
                <flux:input readonly value="{{ $worker->paternal_surname }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.maternal_surname') }}</flux:label>
                <flux:input readonly value="{{ $worker->maternal_surname ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.hired_at') }}</flux:label>
                <flux:input readonly value="{{ Carbon::createFromFormat('Y-m-d',$worker->hired_at)->timezone('America/Lima')->format('d/m/Y') ?? __('common.null') }}" type="text"/>
            </flux:field>
            <flux:input readonly value="{{ $worker->user?->email ?? __('common.null') }}" label="{{ __('common.email') }}" type="email"/>
            <flux:input readonly value="{{ $worker->phone ?? __('common.null') }}" label="{{ __('common.phone') }}" type="text"/>
            <flux:input readonly value="{{ $worker->position ?? __('common.null') }}" label="{{ __('common.position') }}" type="text"/>
            <div class="col-span-full">
                <flux:input readonly value="{{ $worker->address ?? __('common.null') }}" label="{{ __('common.address') }}"
                            type="text"/>
            </div>
            <flux:field>
                <flux:label>{{ trans_choice('auth.user',1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $worker->user?->username ?? __('common.null') }}"
                                type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="#">
                    </flux:button>
                </flux:input.group>
            </flux:field>
            <flux:field>
                <flux:label>{{ trans_choice('role.role',1) }}</flux:label>
                <flux:input.group>
                    <flux:input readonly value="{{ $worker->user?->roles->first()?->name ?? __('common.null') }}"
                                type="text"/>
                    <flux:button type="button" variant="primary" color="cyan"
                                 icon="ellipsis-horizontal" wire:navigate
                                 href="#">
                    </flux:button>
                </flux:input.group>
            </flux:field>
            <div class="col-span-full">
                <flux:input readonly value="{{ $worker->updatedBy->username ?? __('common.inserted_by_null') }}"
                            label="{{ __('common.updated_by') }}" type="text"/>
            </div>
            <flux:input readonly
                        value="{{ $worker->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $worker->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if(!auth()->user()->is($worker->user))
                        @canany(['sys.admin', 'worker.update', 'worker.delete', 'worker.restore'])
                            <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                         href="{{ route('worker.edit', ['workerId' => $worker->id]) }}">
                                {{ __('common.edit') }}
                            </flux:button>
                        @endcanany
                    @endif
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
