<?php

use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use App\Models\Presentation;
use App\Livewire\Forms\PresentationForm;
use Illuminate\Support\Facades\Session;

new class extends Component {
    public PresentationForm $form;

    public function mount(?string $presentationId = null): void
    {
        if ($presentationId) {
            if (!is_numeric($presentationId)) {
                $this->redirectWithError($presentationId);
                return;
            }

            $presentation = Presentation::withTrashed()->find((int)$presentationId);

            if (!$presentation) {
                $this->redirectWithError($presentationId);
                return;
            }

            $this->form->presentation = $presentation;
            $this->form->fill($presentation->toArray());
        }
    }

    protected function redirectWithError($presentationId): void
    {
        Session::flash('error', __('presentation.errors.not_found', ['id' => $presentationId]));
        $this->redirectRoute('presentation.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        try {
            if ($this->form->presentation) {
                $this->form->presentation->update($sanitized);
                Session::flash('success', __('presentation.updated', ['name' => $sanitized['name'], 'id' => $this->form->presentation->id]));
            } else {
                $presentation = Presentation::create($sanitized);
                Session::flash('success', __('presentation.created', ['name' => $sanitized['name'], 'id' => $presentation->id]));
            }
            return redirect()->to(route('presentation.index'));
        } catch (Exception) {
            Session::flash('error', $this->form->presentation ? __('presentation.errors.update_failed') : __('presentation.errors.creation_failed'));
            return redirect()->to(route('presentation.index'));
        }
    }

    public function delete()
    {
        if ($this->form->presentation) {
            if ($this->form->presentation->trashed()) {
                $this->form->presentation->restore();
                Session::flash('success', __('presentation.restored', ['id' => $this->form->presentation->id]));
            } else {
                $this->form->presentation->delete();
                Session::flash('success', __('presentation.deleted', ['id' => $this->form->presentation->id]));
            }
        }
        return redirect()->to(route('presentation.index'));
    }


    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->presentation ? 'presentation.edit' : 'presentation.create')])
            ->title(__($this->form->presentation ? 'views.presentation.edit' : 'views.presentation.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-xl" wire:submit="save">
        @if($this->form->presentation && $this->form->presentation->trashed())
            <x-shared.alert type="info">{{ __('presentation.is_deleted') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.numeric_value') }}</flux:label>
                <flux:input wire:model.live.blur="form.numeric_value" type="decimal" min="0.1" max="99999"/>
                <flux:error name="form.numeric_value"/>
            </flux:field>
            <flux:field class="md:col-span-full">
                <flux:label badge="{{ __('common.required') }}">{{ __('common.description') }}</flux:label>
                <flux:input wire:model.live.blur="form.description" type="text"/>
                <flux:error name="form.description"/>
            </flux:field>

            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->presentation)
                        @canany(['sys.admin', 'presentation.delete', 'presentation.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->presentation->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         icon="{{ $form->presentation->trashed() ? 'arrow-path' : 'trash' }}"
                                         wire:target="delete, save">
                                {{ $this->form->presentation->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'presentation.create', 'presentation.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->presentation ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
