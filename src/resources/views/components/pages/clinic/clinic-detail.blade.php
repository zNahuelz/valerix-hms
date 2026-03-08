<?php

use Livewire\Component;
use App\Models\Clinic;

new class extends Component {
    public ?Clinic $clinic = null;

    public function mount(?string $clinicId = null): void
    {
        if ($clinicId) {
            if (!is_numeric($clinicId)) {
                $this->redirectWithError($clinicId);
                return;
            }

            $clinic = Clinic::withTrashed()->find((int)$clinicId);

            if (!$clinic) {
                $this->redirectWithError($clinicId);
                return;
            }

            $this->clinic = $clinic;
        }
    }

    protected function redirectWithError($clinicId)
    {
        Session::flash('error', __('clinic.errors.not_found', ['id' => $clinicId]));
        $this->redirectRoute('clinic.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('clinic.detail', ['id' => $this->clinic->id, 'name' => ucwords(strtolower($this->clinic->name))])])
            ->title(__('views.clinic.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl">
        @if($clinic && $clinic->trashed())
            <x-shared.alert type="info">{{ __('clinic.is_deleted_alt') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $clinic->name }}" type="text"/>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.ruc') }}</flux:label>
                <flux:input readonly value="{{ $clinic->ruc }}" type="text"/>
            </flux:field>
            <flux:input readonly value="{{ $clinic->address }}" label="{{ __('common.address') }}"
                        type="text"/>
            <flux:input readonly value="{{ $clinic->phone }}" label="{{ __('common.phone') }}" type="text"/>
            <flux:input readonly
                        value="{{ $clinic->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}" type="text"/>
            <flux:input readonly
                        value="{{ $clinic->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}" type="text"/>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'clinic.update', 'clinic.delete', 'clinic.restore'])
                        <flux:button type="button" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                                     href="{{ route('clinic.edit', ['clinicId' => $clinic->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>
