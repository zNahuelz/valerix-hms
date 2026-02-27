<?php

use Livewire\Component;
use App\Models\Holiday;
use App\Livewire\Forms\HolidayForm;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public HolidayForm $form;

    public function mount(?string $holidayId = null): void
    {
        if ($holidayId) {
            if (!is_numeric($holidayId)) {
                $this->redirectWithError($holidayId);
                return;
            }

            $holiday = Holiday::withTrashed()->find((int)$holidayId);

            if (!$holiday) {
                $this->redirectWithError($holidayId);
                return;
            }

            $this->form->holiday = $holiday;
            $this->form->fill($holiday->toArray());
        }
    }

    protected function redirectWithError($holidayId)
    {
        Session::flash('error', __('holiday.errors.not_found', ['id' => $holidayId]));
        $this->redirectRoute('holiday.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->holiday) {
            $this->form->holiday->update($sanitized);
            Session::flash('success', __('holiday.updated', ['name' => $sanitized['name'], 'id' => $this->form->holiday->id, 'date' => $sanitized['date']]));
        } else {
            $holiday = Holiday::create($sanitized);
            Session::flash('success', __('holiday.created', ['name' => $sanitized['name'], 'id' => $holiday->id, 'date' => $sanitized['date']]));
        }
        return redirect()->to(route('holiday.index'));
    }

    public function delete()
    {
        if ($this->form->holiday) {
            $this->form->holiday->forceDelete();
            Session::flash('success', __('holiday.deleted', ['id' => $this->form->holiday->id, 'date' => $this->form->holiday->date]));
        }
        return redirect()->to(route('holiday.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->holiday ? 'holiday.edit' : 'holiday.create')])
            ->title(__($this->form->holiday ? 'views.holiday.edit' : 'views.holiday.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-sm" wire:submit="save">
        <flux:fieldset class="grid grid-cols-1 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.date') }}</flux:label>
                <flux:input wire:model.live.blur="form.date" type="date"/>
                <flux:error name="form.date"/>
            </flux:field>
            <div class="inline-flex gap-2 items-center">
                <input
                    type="checkbox"
                    wire:model.live="form.is_recurring"
                    class="w-4 h-4 border border-lg rounded-2xl bg-accent focus:ring-2 focus:ring-accent"
                />
                <flux:label>{{ __('common.recurring_holiday') }}</flux:label>
            </div>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->holiday)
                        @canany(['sys.admin', 'holiday.delete',])
                            <flux:button type="button" variant="primary"
                                         color="red"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{  __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'holiday.create', 'holiday.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="delete, save">
                            {{ $this->form->holiday ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
