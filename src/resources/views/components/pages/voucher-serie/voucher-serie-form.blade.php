<?php

use Livewire\Component;
use App\Models\VoucherSerie;
use App\Models\VoucherType;
use App\Livewire\Forms\Voucher\VoucherSerieForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;

new class extends Component
{
    public VoucherSerieForm $form;

    public function mount(?string $voucherSerieId = null): void
    {
        $this->form->voucher_type_id = $this->voucherTypes->first()->id;
        if ($voucherSerieId) {
            if (!is_numeric($voucherSerieId)) {
                $this->redirectWithError($voucherSerieId);
                return;
            }

            $voucherSerie = VoucherSerie::find((int)$voucherSerieId);

            if (!$voucherSerie) {
                $this->redirectWithError($voucherSerieId);
                return;
            }

            $this->form->voucherSerie = $voucherSerie;
            $this->form->fill($voucherSerie->toArray());
            $this->form->serie_number = (int) substr($voucherSerie->serie, 1);
        }
    }

    protected function redirectWithError($voucherSerieId)
    {
        Session::flash('error', __('voucher-serie.errors.not_found', ['id' => $voucherSerieId]));
        $this->redirectRoute('voucherSerie.index');
    }

    #[Computed]
    public function voucherTypes()
    {
        return VoucherType::orderBy('name')->get();
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->voucherSerie) {
            $this->form->voucherSerie->update($sanitized);
            $this->form->voucherSerie->refresh();
            if($this->form->voucherSerie->is_active){
                VoucherSerie::where('is_active', true)
                    ->where('voucher_type_id', $this->form->voucherSerie->voucher_type_id)
                    ->where('id', '!=', $this->form->voucherSerie->id)
                    ->update(['is_active' => false]);
            }
            Session::flash('success', __('voucher-serie.updated', ['name' => $sanitized['serie'], 'id' => $this->form->voucherSerie->id]));
        } else {
            $voucherSerie = VoucherSerie::create($sanitized);
            if($voucherSerie->is_active){
                VoucherSerie::where('is_active', true)
                    ->where('voucher_type_id', $voucherSerie->voucher_type_id)
                    ->where('id', '!=', $voucherSerie->id)
                    ->update(['is_active' => false]);
            }
            Session::flash('success', __('voucher-serie.created', ['name' => $sanitized['serie'], 'id' => $voucherSerie->id]));
        }
        return redirect()->to(route('voucherSerie.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->voucherSerie ? 'voucher-serie.edit' : 'voucher-serie.create')])
            ->title(__($this->form->voucherSerie ? 'views.voucher_serie.edit' : 'views.voucher_serie.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-xl" wire:submit="save">
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('voucher-type.voucher_type',1) }}</flux:label>
                <flux:select wire:model.live="form.voucher_type_id" :disabled="(bool)$this->form->voucherSerie">
                    @foreach($this->voucherTypes as $type)
                        <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{__('voucher-serie.serie')}}</flux:label>
                <flux:input wire:model.live.blur="form.serie_number" type="number" min="1" max="999"
                            :disabled="(bool)$this->form->voucherSerie" />
                <flux:error name="form.serie_number"/>
                <flux:error name="form.serie"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.next_value') }}</flux:label>
                <flux:input wire:model.live.blur="form.next_value" type="number"/>
                <flux:error name="form.next_value"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{__('common.status')}}</flux:label>
                <flux:checkbox wire:model="form.is_active" label="{{ __('common.status') }}" />
            </flux:field>
            @if($this->form->voucher_type_id && $form->serie_number)
                <div class="text-sm text-gray-500 dark:text-white col-span-full">
                    {{ __('voucher-serie.serie_preview') }}:
                    <span class="font-bold">{{ $form->computedSerie() }}</span>
                </div>
            @endif
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'voucherSerie.create', 'voucherSerie.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="save">
                            {{ $this->form->voucherSerie ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
