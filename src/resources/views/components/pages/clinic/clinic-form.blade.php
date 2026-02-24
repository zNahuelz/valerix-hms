<?php

use Livewire\Component;
use App\Models\Clinic;
use App\Livewire\Forms\ClinicForm;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public ClinicForm $form;

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

            $this->form->clinic = $clinic;
            $this->form->fill($clinic->toArray());
        }
    }

    protected function redirectWithError($clinicId)
    {
        Session::flash('error', __('clinic.errors.not_found', ['id' => $clinicId]));
        $this->redirectRoute('clinic.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->clinic) {
            $this->form->clinic->update($sanitized);
            Session::flash('success', __('clinic.updated', ['name' => $sanitized['name'], 'id' => $this->form->clinic->id]));
        } else {
            $clinic = Clinic::create($sanitized);
            Session::flash('success', __('clinic.created', ['name' => $sanitized['name'], 'id' => $clinic->id]));
        }
        return redirect()->to(route('clinic.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->clinic ? 'clinic.edit' : 'clinic.create')])
            ->title(__($this->form->clinic ? 'views.clinic.edit' : 'views.clinic.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">
        @if($this->form->clinic && $this->form->clinic->trashed())
            <x-shared.alert type="info">{{ __('clinic.is_deleted') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.ruc') }}</flux:label>
                <flux:input wire:model.live.blur="form.ruc" type="text"/>
                <flux:error name="form.ruc"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.address') }}</flux:label>
                <flux:input wire:model.live.blur="form.address"  type="text"/>
                <flux:error name="form.address"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.phone') }}</flux:label>
                <flux:input wire:model.live.blur="form.phone" type="text"/>
                <flux:error name="form.phone"/>
            </flux:field>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'clinic.create', 'clinic.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="save">
                            {{ $this->form->clinic ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
