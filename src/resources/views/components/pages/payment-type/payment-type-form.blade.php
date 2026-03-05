<?php

use Livewire\Component;
use App\Models\PaymentType;
use App\Livewire\Forms\PaymentTypeForm;
use Illuminate\Support\Facades\Session;
use App\Enums\PaymentAction;

new class extends Component
{
    public PaymentTypeForm $form;

    public function mount(?string $paymentTypeId = null): void
    {
        $this->form->action = PaymentAction::cases()[0]->value;
        if ($paymentTypeId) {
            if (!is_numeric($paymentTypeId)) {
                $this->redirectWithError($paymentTypeId);
                return;
            }

            $paymentType = PaymentType::find((int)$paymentTypeId);

            if (!$paymentType) {
                $this->redirectWithError($paymentTypeId);
                return;
            }

            $this->form->paymentType = $paymentType;
            $this->form->action = $paymentType->getRawOriginal('action');
            $this->form->fill($paymentType->toArray());
        }
    }

    protected function redirectWithError($paymentTypeId)
    {
        Session::flash('error', __('payment-type.errors.not_found', ['id' => $paymentTypeId]));
        $this->redirectRoute('paymentType.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        if ($this->form->paymentType) {
            $this->form->paymentType->update($sanitized);
            Session::flash('success', __('payment-type.updated', ['name' => $sanitized['name'], 'id' => $this->form->paymentType->id]));
        } else {
            $paymentType = PaymentType::create($sanitized);
            Session::flash('success', __('payment-type.created', ['name' => $sanitized['name'], 'id' => $paymentType->id]));
        }
        return redirect()->to(route('paymentType.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->paymentType ? 'payment-type.edit' : 'payment-type.create')])
            ->title(__($this->form->paymentType ? 'views.payment_type.edit' : 'views.payment_type.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-xl" wire:submit="save">
        <x-shared.alert><span class="whitespace-pre-line">{{__('payment-type.info')}}</span></x-shared.alert>
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input wire:model.live.blur="form.name" type="text"/>
                <flux:error name="form.name"/>
            </flux:field>

            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ trans_choice('common.action',1) }}</flux:label>
                <flux:select wire:model.live.blur="form.action">
                    @foreach(PaymentAction::cases() as $action)
                        <flux:select.option
                            value="{{$action->value}}">{{$action->label()}}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="form.action"/>
            </flux:field>
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'paymentType.create', 'paymentType.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled" wire:target="save">
                            {{ $this->form->paymentType ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
