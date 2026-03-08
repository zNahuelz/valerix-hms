<?php

use App\Enums\BuyOrderStatus;
use App\Livewire\Forms\BuyOrderForm;
use App\Models\BuyOrder;
use App\Models\Clinic;
use App\Models\Medicine;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Livewire\Component;

new class extends Component {
    public BuyOrderForm $form;

    public array $clinics = [];
    public array $suppliers = [];

    public string $barcode = '';
    public ?string $barcodeError = null;


    public function mount(?string $buyOrderId = null): void
    {
        if (!Clinic::whereNull('deleted_at')->exists()) {
            Session::flash('error', __('buy-order.errors.creation_disabled_empty_clinics'));
            $this->redirectRoute('buyOrder.index');
            return;
        }
        $this->clinics = Clinic::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();

        if (!Supplier::whereNull('deleted_at')->exists()) {
            Session::flash('error', __('buy-order.errors.creation_disabled_empty_suppliers'));
            $this->redirectRoute('buyOrder.index');
            return;
        }
        $this->suppliers = Supplier::select(['id', 'name'])->whereNull('deleted_at')->orderBy('name')->get()->toArray();

        $this->form->clinic_id = $this->clinics[0]['id'];
        $this->form->supplier_id = $this->suppliers[0]['id'];
        $this->form->status = BuyOrderStatus::REQUEST_SENT->value;

        if ($buyOrderId) {
            if (!is_numeric($buyOrderId)) {
                $this->redirectWithError($buyOrderId);
                return;
            }

            $buyOrder = BuyOrder::withTrashed()->with('buyOrderDetails.medicine')->find((int)$buyOrderId);

            if (!$buyOrder) {
                $this->redirectWithError($buyOrderId);
                return;
            }
            $this->form->buyOrder = $buyOrder;
            $this->form->clinic_id = $buyOrder->clinic_id;
            $this->form->supplier_id = $buyOrder->supplier_id;
            $this->form->tax = $buyOrder->tax;
            $this->form->subtotal = $buyOrder->subtotal;
            $this->form->total = $buyOrder->total;
            $this->form->status = $buyOrder->getRawOriginal('status');
            $this->form->buy_order_details = $buyOrder->buyOrderDetails
                ->map(fn($d) => [
                    'medicine_id' => $d->medicine_id,
                    'medicine_name' => $d->medicine->name ?? '',
                    'barcode' => $d->medicine->barcode ?? '',
                    'amount' => $d->amount,
                    'unit_price' => $d->unit_price,
                    'line_total' => $d->amount * $d->unit_price,
                ])
                ->toArray();
        }
    }

    protected function redirectWithError($buyOrderId)
    {
        Session::flash('error', __('buy-order.errors.not_found', ['id' => $buyOrderId]));
        $this->redirectRoute('buyOrder.index');
    }

    public function addByBarcode(): void
    {
        $this->barcodeError = null;

        if (blank($this->barcode)) {
            return;
        }

        $medicine = Medicine::where('barcode', trim($this->barcode))->first();

        if (!$medicine) {
            $this->barcodeError = __('validation.medicine_barcode.not_found');
            return;
        }

        // Check for duplicates
        foreach ($this->form->buy_order_details as $detail) {
            if ($detail['medicine_id'] === $medicine->id) {
                $this->barcodeError = __('validation.medicine_barcode.already_added');
                return;
            }
        }

        $this->form->buy_order_details[] = [
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name,
            'barcode' => $medicine->barcode,
            'amount' => 1,
            'unit_price' => 0,
            'line_total' => 0,
        ];

        $this->barcode = '';
        $this->recalculate();
    }

    public function removeDetail(int $index): void
    {
        array_splice($this->form->buy_order_details, $index, 1);
        $this->recalculate();
    }


    public function updated(string $propertyName): void
    {
        if (
            str($propertyName)->startsWith('form.buy_order_details') ||
            $propertyName === 'form.tax'
        ) {
            $this->recalculate();
        }
    }

    private function recalculate(): void
    {
        $subtotal = 0;

        foreach ($this->form->buy_order_details as $i => $detail) {
            $lineTotal = (float)($detail['amount'] ?? 0) * (float)($detail['unit_price'] ?? 0);
            $this->form->buy_order_details[$i]['line_total'] = $lineTotal;
            $subtotal += $lineTotal;
        }

        $this->form->subtotal = round($subtotal, 2);
        $this->form->total = round($subtotal + (float)($this->form->tax ?? 0), 2);
    }

    public function save()
    {
        $this->recalculate();
        $this->validate();
        $data = $this->form->sanitized();

        try {
            if ($this->form->buyOrder) {
                $this->form->buyOrder->update([
                    'clinic_id' => $data['clinic_id'],
                    'supplier_id' => $data['supplier_id'],
                    'tax' => $data['tax'],
                    'subtotal' => $data['subtotal'],
                    'total' => $data['total'],
                    'status' => $data['status'],
                ]);

                $this->form->buyOrder->buyOrderDetails()->delete();

                foreach ($data['buy_order_details'] as $detail) {
                    $this->form->buyOrder->buyOrderDetails()->create([
                        'medicine_id' => $detail['medicine_id'],
                        'amount' => $detail['amount'],
                        'unit_price' => $detail['unit_price'],
                    ]);
                }

                Session::flash('success', __('buy-order.updated', ['id' => $this->form->buyOrder->id]));
            } else {
                $buyOrder = BuyOrder::create([
                    'clinic_id' => $data['clinic_id'],
                    'supplier_id' => $data['supplier_id'],
                    'tax' => $data['tax'],
                    'subtotal' => $data['subtotal'],
                    'total' => $data['total'],
                    'status' => $data['status'],
                ]);

                foreach ($data['buy_order_details'] as $detail) {
                    $buyOrder->buyOrderDetails()->create([
                        'medicine_id' => $detail['medicine_id'],
                        'amount' => $detail['amount'],
                        'unit_price' => $detail['unit_price'],
                    ]);
                }

                Session::flash('success', __('buy-order.created', ['id' => $buyOrder->id]));
            }
            return redirect()->to(route('buyOrder.index'));
        } catch (Exception) {
            Session::flash('error', $this->form->buyOrder ? __('buy-order.errors.update_failed') : __('buy-order.errors.creation_failed'));
            return redirect()->to(route('buyOrder.index'));
        }
    }

    public function delete()
    {
        if ($this->form->buyOrder) {
            if ($this->form->buyOrder->trashed()) {
                $this->form->buyOrder->restore();
                Session::flash('success', __('buy-order.restored', ['id' => $this->form->buyOrder->id]));
            } else {
                $this->form->buyOrder->delete();
                Session::flash('success', __('buy-order.deleted', ['id' => $this->form->buyOrder->id]));
            }
        }
        return redirect()->to(route('buyOrder.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->buyOrder ? 'buy-order.edit' : 'buy-order.create')])
            ->title(__($this->form->buyOrder ? 'views.buy_order.edit' : 'views.buy_order.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-4xl" wire:submit="save">
        <flux:fieldset wire:loading.attr="disabled" wire:target="save, delete">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ trans_choice('clinic.clinic', 1) }}</flux:label>
                    <flux:select wire:model.live.blur="form.clinic_id">
                        @foreach ($clinics as $clinic)
                            <flux:select.option value="{{ $clinic['id'] }}">{{ $clinic['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.clinic_id"/>
                </flux:field>

                <flux:field>
                    <flux:label
                        badge="{{ __('common.required') }}">{{ trans_choice('supplier.supplier', 1) }}</flux:label>
                    <flux:select wire:model.live.blur="form.supplier_id">
                        @foreach ($suppliers as $supplier)
                            <flux:select.option
                                value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.supplier_id"/>
                </flux:field>

                <flux:field>
                    <flux:label badge="{{ __('common.required') }}">{{ __('common.status') }}</flux:label>
                    <flux:select wire:model.live.blur="form.status">
                        @foreach(BuyOrderStatus::cases() as $status)
                            <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.status"/>
                </flux:field>
            </div>
            <div class="mt-6">
                <flux:separator text="{{ trans_choice('medicine.medicine', 2) }}"/>
                <div class="mt-3 flex flex-col sm:flex-row gap-2">
                    <flux:field class="flex-1">
                        <flux:label>{{ __('common.barcode') }}</flux:label>
                        <flux:input
                            wire:model="barcode"
                            wire:keydown.enter.prevent="addByBarcode"
                            type="text"
                            :placeholder="__('common.barcode_placeholder')"
                            autocomplete="off"
                        />
                        @if ($barcodeError)
                            <flux:error>{{ $barcodeError }}</flux:error>
                        @endif
                    </flux:field>
                    <div class="flex items-end">
                        <flux:button
                            type="button"
                            variant="primary"
                            wire:click="addByBarcode"
                            icon="plus"
                        >
                            {{ __('common.add') }}
                        </flux:button>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                @if (count($form->buy_order_details) > 0)

                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('common.barcode') }}</flux:table.column>
                            <flux:table.column>{{ trans_choice('medicine.medicine', 1) }}</flux:table.column>
                            <flux:table.column>{{ __('common.amount') }}</flux:table.column>
                            <flux:table.column>{{ __('common.unit_price') }}</flux:table.column>
                            <flux:table.column>{{ __('common.subtotal') }}</flux:table.column>
                            <flux:table.column></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($form->buy_order_details as $index => $detail)
                                <flux:table.row wire:key="detail-{{ $index }}">

                                    <flux:table.cell class="font-mono text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $detail['barcode'] ?? '—' }}
                                    </flux:table.cell>

                                    <flux:table.cell class="font-medium">
                                        {{ $detail['medicine_name'] ?? '—' }}
                                        <flux:error name="form.buy_order_details.{{ $index }}.medicine_id"/>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:input
                                            wire:model.live="form.buy_order_details.{{ $index }}.amount"
                                            type="number"
                                            min="1"
                                            max="99999"
                                            size="sm"
                                            class="w-24 text-right"
                                        />
                                        <flux:error name="form.buy_order_details.{{ $index }}.amount"/>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:input
                                            wire:model.live="form.buy_order_details.{{ $index }}.unit_price"
                                            type="number"
                                            min="0.01"
                                            step="any"
                                            size="sm"
                                            class="w-32 text-right"
                                        />
                                        <flux:error name="form.buy_order_details.{{ $index }}.unit_price"/>
                                    </flux:table.cell>

                                    <flux:table.cell class="font-mono text-right tabular-nums">
                                        {{ number_format($detail['line_total'] ?? 0, 2) }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <flux:button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            inset="top bottom"
                                            wire:click="removeDetail({{ $index }})"
                                            class="text-red-500 hover:text-red-700"
                                        />
                                    </flux:table.cell>

                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>

                    <flux:error name="form.buy_order_details"/>

                @else
                    <div
                        class="flex flex-col items-center justify-center py-10 border border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-400 dark:text-zinc-500">
                        <flux:icon name="shopping-cart" class="size-8 mb-2 opacity-40"/>
                        <p class="text-sm">{{ __('treatment.errors.no_medicines') }}</p>
                    </div>
                    <flux:error name="form.buy_order_details"/>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <div class="w-full sm:w-72 space-y-2">

                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ strtoupper(__('common.subtotal')) }}
                        </span>
                        <flux:input
                            wire:model="form.subtotal"
                            type="number"
                            readonly
                            size="sm"
                            class="w-40 text-right bg-zinc-50 dark:bg-zinc-800 cursor-not-allowed"
                        />
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <flux:label class="text-sm" badge="{{ __('common.required') }}">
                            {{ strtoupper(__('common.tax')) }}
                        </flux:label>
                        <div class="w-40">
                            <flux:input
                                wire:model.live="form.tax"
                                type="number"
                                min="0"
                                step="any"
                                size="sm"
                                class="text-right w-full"
                            />
                            <flux:error name="form.tax"/>
                        </div>
                    </div>
                    <flux:separator/>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                            {{ strtoupper(__('common.total')) }}
                        </span>
                        <flux:input
                            wire:model="form.total"
                            type="number"
                            readonly
                            size="sm"
                            class="w-40 text-right bg-zinc-50 dark:bg-zinc-800 cursor-not-allowed font-bold"
                        />
                    </div>

                </div>
            </div>
            <div class="mt-6 col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->buyOrder)
                        @canany(['sys.admin', 'buyOrder.delete', 'buyOrder.restore'])
                            <flux:button type="button" variant="primary"
                                         color="{{ $this->form->buyOrder->trashed() ? 'amber' : 'red' }}"
                                         wire:click="delete"
                                         class="w-full md:w-auto" wire:loading.attr="disabled"
                                         wire:target="delete, save">
                                {{ $this->form->buyOrder->trashed() ? __('common.restore') : __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif
                    @canany(['sys.admin', 'buy_order.create', 'buy_order.update'])
                        <flux:button
                            type="submit"
                            variant="primary"
                            class="w-full md:w-auto md:ml-auto"
                            wire:loading.attr="disabled"
                            wire:target="delete, save"
                            icon="{{ $this->form->buyOrder ? 'check' : 'plus' }}"
                        >
                            {{ $this->form->buyOrder ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>

