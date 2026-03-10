<?php

use Livewire\Component;
use App\Models\Setting;
use App\Enums\SettingType;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public ?Setting $setting = null;

    public function mount(?string $settingId = null): void
    {
        if ($settingId) {
            if (!is_numeric($settingId)) {
                $this->redirectWithError($settingId);
                return;
            }

            $setting = Setting::with(['createdBy', 'updatedBy'])->find((int)$settingId);

            if (!$setting) {
                $this->redirectWithError($settingId);
                return;
            }

            $this->setting = $setting;
        }
    }

    protected function redirectWithError($settingId): void
    {
        Session::flash('error', __('setting.errors.not_found', ['id' => $settingId]));
        $this->redirectRoute('setting.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('setting.detail', ['id' => $this->setting?->id, 'key' => $this->setting?->key])])
            ->title(__('views.setting.detail'));
    }
};
?>

<div class="flex justify-center px-4">
    <div class="w-full md:max-w-3xl space-y-2">
        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:field>
                <flux:label>{{ __('common.key') }}</flux:label>
                <flux:input readonly value="{{ $setting->_key }}" type="text" class="font-mono"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('common.value_type') }}</flux:label>
                @php
                    $type = $setting->value_type instanceof SettingType
                        ? $setting->value_type
                        : SettingType::from($setting->value_type);
                @endphp
                <flux:input readonly value="{{ $type->label() }}" type="text"/>
            </flux:field>

            <flux:field class="col-span-full">
                <flux:label>{{ __('common.value') }}</flux:label>
                <br>
                @if($type === SettingType::BOOLEAN)
                    @php $boolVal = filter_var($setting->value, FILTER_VALIDATE_BOOLEAN); @endphp
                    <flux:badge color="{{ $boolVal ? 'green' : 'red' }}" size="lg" inset="top bottom">
                        {{ $boolVal ? __('common.true') : __('common.false') }}
                    </flux:badge>
                @else
                    <flux:input readonly value="{{ $setting->value }}" type="text" class="font-mono"/>
                @endif
            </flux:field>

            <flux:field class="col-span-full">
                <flux:label>{{ __('common.description') }}</flux:label>
                <flux:input readonly value="{{ $setting->description ?? __('common.null') }}" type="text"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('common.created_by') }}</flux:label>
                <flux:input readonly value="{{ $setting->createdBy?->username ?? __('common.inserted_by_null') }}" type="text"/>
            </flux:field>

            <flux:field>
                <flux:label>{{ __('common.updated_by') }}</flux:label>
                <flux:input readonly value="{{ $setting->updatedBy?->username ?? __('common.inserted_by_null') }}" type="text"/>
            </flux:field>

            <flux:input readonly
                        value="{{ $setting->created_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.created_at') }}"
                        type="text"/>

            <flux:input readonly
                        value="{{ $setting->updated_at?->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                        label="{{ __('common.updated_at_alt') }}"
                        type="text"/>

            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-end gap-2">
                    @canany(['sys.admin', 'setting.update'])
                        <flux:button
                            type="button"
                            variant="primary"
                            class="w-full md:w-auto"
                            wire:navigate
                            href="{{ route('setting.edit', ['settingId' => $setting->id]) }}"
                        >
                            {{ __('common.edit') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>

        </flux:fieldset>
    </div>
</div>
