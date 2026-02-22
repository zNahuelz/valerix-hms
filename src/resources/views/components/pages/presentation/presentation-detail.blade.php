<?php use Livewire\Component;
use App\Models\Presentation;
new class extends Component {
    public ?Presentation
    $presentation = null;
    public function mount(?string $presentationId = null): void
    {
        if ($presentationId) {
            if
            (!is_numeric($presentationId)) {
                $this->redirectWithError($presentationId);
                return;
            }

            $presentation = Presentation::withTrashed()->find((int) $presentationId);

            if (!$presentation) {
                $this->redirectWithError($presentationId);
                return;
            }
            $this->presentation = $presentation;
        }
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', [
                'heading' => __('presentation.detail', [
                    'id' => $this->presentation->id,
                    'name' =>
                        ucwords(strtolower($this->presentation->description)),

                ])
            ])
            ->title(__('views.presentation.detail'));
    }
};
    ?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-lg">
        @if($this->presentation && $this->presentation->trashed())
            <x-shared.alert type="info">{{ __('presentation.is_deleted_alt') }}</x-shared.alert>
        @endif
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <flux:field>
                <flux:label>{{ trans_choice('common.name', 1) }}</flux:label>
                <flux:input readonly value="{{ $this->presentation->name }}" type="text" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('common.numeric_value') }}</flux:label>
                <flux:input readonly value="{{ $this->presentation->numeric_value }}" type="text" />
            </flux:field>
            <flux:field class="md:col-span-full">
                <flux:label>{{ __('common.description') }}</flux:label>
                <flux:input readonly value="{{ $this->presentation->description }}" type="text" />
            </flux:field>
            <flux:input readonly
                value="{{ $presentation->created_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                label="{{ __('common.created_at') }}" type="text" />
            <flux:input readonly
                value="{{ $presentation->updated_at->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                label="{{ __('common.updated_at_alt') }}" type="text" />
            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @canany(['sys.admin', 'presentation.create', 'presentation.update'])
                        <flux:button type="submit" variant="primary" class="w-full md:w-auto md:ml-auto" wire:navigate
                            href="{{ route('presentation.edit', ['presentationId' => $this->presentation->id]) }}">
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </div>
</div>