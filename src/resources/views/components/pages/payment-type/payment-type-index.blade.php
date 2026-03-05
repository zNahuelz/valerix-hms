<?php

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\PaymentType;
use Livewire\Attributes\Computed;

new class extends Component
{
    use WithPagination, WithoutUrlPagination;

    public string $keyword = '';
    public string $searchColumn = 'id';
    public bool $searching = false;

    protected function rules(): array
    {
        $rules = [
            'id' => ['regex:/^\d+$/'],
            'name' => ['required','string', 'min:3']
        ];
        return [
            'searchColumn' => ['required', 'in:id,name'],
            'keyword' => $rules[$this->searchColumn] ?? ['string', 'min:3']
        ];
    }

    public function updatedKeyword(): void
    {
        $this->searching = false;
    }

    public function updatedSearchColumn(): void
    {
        $this->keyword = '';
        $this->searching = false;
        $this->resetValidation();
    }

    public function search(): void
    {
        $this->keyword = trim($this->keyword);
        $this->validate();
        $this->searching = true;
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->reset();
        $this->searching = false;
        $this->resetValidation();
        $this->resetPage();
    }

    #[Computed]
    public function paymentTypes()
    {
        $query = PaymentType::query();

        if ($this->searching && $this->keyword) {
            switch ($this->searchColumn) {
                case 'id':
                    $query->where('id', $this->keyword);
                    break;
                case 'name':
                    $query->whereLike('name', "%{$this->keyword}%", caseSensitive: false);
                    break;
            }
        }

        return $query->orderBy('updated_at', 'desc')->paginate(20);
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('payment-type.index')])
            ->title(__('views.payment_type.index'));
    }
};
?>

<div>
    <div class="flex flex-col md:flex-row w-full items-stretch md:items-center justify-between gap-4 mb-2">
        @canany(['sys.admin', 'paymentType.create'])
            <flux:button variant="primary" icon="plus" wire:navigate href="{{ route('paymentType.create') }}"
                         class="w-full md:w-auto">
                {{ __('common.new') }}
            </flux:button>
        @endcanany
        <form wire:submit.prevent="search"
              class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full md:w-auto md:ml-auto">
            <flux:select wire:model.live="searchColumn" class="w-full md:w-40">
                <flux:select.option value="id">{{ __('common.id') }}</flux:select.option>
                <flux:select.option value="name">{{ trans_choice('common.name', 1) }}</flux:select.option>
            </flux:select>
            <flux:input wire:model="keyword" :placeholder="__('common.search') . '...'" class="w-full md:w-64"/>
            <flux:button.group>
                <flux:button type="button" icon="arrow-path" square wire:click="resetSearch"
                             wire:loading.attr="disabled" wire:target="resetSearch" class="w-full md:w-auto p-3"/>
                <flux:button type="submit" icon="magnifying-glass" wire:loading.attr="disabled" wire:target="search"
                             class="w-full md:w-auto">
                    {{ __('common.search') }}
                </flux:button>
            </flux:button.group>
        </form>
    </div>
    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('common.id_alt') }}</flux:table.column>
            <flux:table.column>{{ trans_choice('common.name', 1) }}</flux:table.column>
            <flux:table.column>{{ trans_choice('common.action',1) }}</flux:table.column>
            <flux:table.column>{{ __('common.created_at')  }}</flux:table.column>
            <flux:table.column>{{ __('common.updated_at') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($this->paymentTypes as $paymentType)
                <flux:table.row class="hover:bg-accent-content/10">
                    <flux:table.cell>{{ $paymentType->id }}</flux:table.cell>
                    <flux:table.cell>{{ $paymentType->name }}</flux:table.cell>
                    <flux:table.cell>{{ $paymentType->action->label() }}</flux:table.cell>
                    <flux:table.cell>{{ $paymentType->created_at->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>{{ $paymentType->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') }}</flux:table.cell>
                    <flux:table.cell>
                            @canany(['sys.admin', 'paymentType.edit'])
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom"
                                             title="{{ __('common.edit') }}"
                                             href="{{ route('paymentType.edit', ['paymentTypeId' => $paymentType->id]) }}"
                                             wire:navigate>
                                </flux:button>
                            @endcanany
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-lg md:text-xl font-light">
                        {{ __('payment-type.errors.empty_set') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <x-shared.pagination :paginator="$this->paymentTypes"></x-shared.pagination>
</div>
