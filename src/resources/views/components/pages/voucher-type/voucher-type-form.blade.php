<?php

use Livewire\Component;
use App\Models\VoucherType;
use App\Livewire\Forms\Voucher\VoucherTypeForm;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public VoucherTypeForm $form;

    public function mount(?string $voucherTypeId = null): void
    {
        if ($voucherTypeId) {
            if (!is_numeric($voucherTypeId)) {
                $this->redirectWithError($voucherTypeId);
                return;
            }

            $voucherType = VoucherType::withTrashed()->find((int)$voucherTypeId);

            if (!$voucherType) {
                $this->redirectWithError($voucherTypeId);
                return;
            }

            $this->form->voucherType = $voucherType;
            $this->form->fill($voucherType->toArray());
        }
    }

    protected function redirectWithError($voucherTypeId)
    {
        Session::flash('error', __('voucher-type.errors.not_found', ['id' => $voucherTypeId]));
        $this->redirectRoute('voucherType.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->voucherType) {
            $this->form->voucherType->update($sanitized);
            Session::flash('success', __('voucher-type.updated', ['name' => $sanitized['name'], 'id' => $this->form->voucherType->id]));
        } else {
            $voucherType = VoucherType::create($sanitized);
            Session::flash('success', __('voucher-type.created', ['name' => $sanitized['name'], 'id' => $voucherType->id]));
        }
        return redirect()->to(route('voucherType.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->voucherType ? 'voucher-type.edit' : 'voucher-type.create')])
            ->title(__($this->form->voucherType ? 'views.voucher_type.edit' : 'views.voucher_type.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-xs" wire:submit="save">
        <flux:fieldset class="grid grid-cols-1 gap-3" wire:loading.attr="disabled"
                       wire:target="save">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>

            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'voucherType.create', 'voucherType.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="save">
                            {{ $this->form->voucherType ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
