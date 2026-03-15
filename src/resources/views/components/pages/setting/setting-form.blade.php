<?php

use Livewire\Component;
use App\Enums\SettingType;
use App\Models\Setting;
use App\Livewire\Forms\SettingForm;
use Illuminate\Support\Facades\Session;

new class extends Component {
    public SettingForm $form;

    public function mount(?string $settingId = null): void
    {
        if ($settingId) {
            if (!is_numeric($settingId)) {
                $this->redirectWithError($settingId);
                return;
            }

            $setting = Setting::find((int)$settingId);

            if (!$setting) {
                $this->redirectWithError($settingId);
                return;
            }

            $valueType = $setting->value_type instanceof SettingType
                ? $setting->value_type->value
                : $setting->value_type;

            $this->form->setting = $setting;
            $this->form->fill([
                'key' => $setting->key,
                'value' => $valueType === SettingType::BOOLEAN->value
                    ? (bool)$setting->value
                    : $setting->value,
                'value_type' => $valueType,
                'description' => $setting->description,
            ]);
        } else {
            $this->form->value_type = SettingType::STRING->value;
        }
    }

    protected function redirectWithError($settingId): void
    {
        Session::flash('error', __('setting.errors.not_found', ['id' => $settingId]));
        $this->redirectRoute('setting.index');
    }

    public function updatedFormKey(): void
    {
        $this->form->key = strtoupper(str_replace([' ', '-'], '_', trim($this->form->key)));
    }

    public function onValueTypeChanged(string $type): void
    {
        $this->form->value_type = $type;
        $this->form->value = $type === SettingType::BOOLEAN->value ? false : '';
        $this->resetValidation('form.value');
    }

    public function save()
    {
        $this->validate();
        $sanitized = $this->form->sanitized();
        try {
            if ($this->form->setting) {
                $this->form->setting->update($sanitized);
                Session::flash('success', __('setting.updated', ['key' => $sanitized['key'], 'id' => $this->form->setting->id]));
            } else {
                $setting = Setting::create($sanitized);
                Session::flash('success', __('setting.created', ['key' => $sanitized['key'], 'id' => $setting->id]));
            }
            return redirect()->to(route('setting.index'));
        } catch (Exception) {
            Session::flash('error', $this->form->setting
                ? __('setting.errors.update_failed')
                : __('setting.errors.creation_failed'));
            return redirect()->to(route('setting.index'));
        }
    }

    public function delete()
    {
        if ($this->form->setting) {
            $this->form->setting->delete();
            Session::flash('success', __('setting.deleted', ['id' => $this->form->setting->id]));
        }
        return redirect()->to(route('setting.index'));
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __($this->form->setting ? 'setting.edit' : 'setting.create')])
            ->title(__($this->form->setting ? 'views.setting.edit' : 'views.setting.create'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-3xl" wire:submit="save">

        @if($this->form->setting)
            <x-shared.alert type="warning">{{ __('setting.deletion_warning') }}</x-shared.alert>
        @else
            <x-shared.alert type="info">{{ __('setting.creation_warning') }}</x-shared.alert>
        @endif

        <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-3" wire:loading.attr="disabled"
                       wire:target="save, delete">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.key') }}</flux:label>
                <flux:input wire:model.live.blur="form.key" type="text"
                            placeholder="{{__('common.key_placeholder')}}"/>
                <flux:error name="form.key"/>
            </flux:field>

            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.value_type') }}</flux:label>
                <flux:select wire:change="onValueTypeChanged($event.target.value)">
                    @foreach(SettingType::cases() as $type)
                        <option value="{{ $type->value }}"
                            @selected($this->form->value_type === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </flux:select>
                <flux:error name="form.value_type"/>
            </flux:field>

            <flux:field class="col-span-full">
                <flux:label badge="{{ __('common.required') }}">{{ __('common.value') }}</flux:label>

                @if($this->form->value_type === SettingType::BOOLEAN->value)
                    <flux:switch wire:model.live="form.value"
                                 :label="$this->form->value ? __('common.true') : __('common.false')"/>

                @elseif($this->form->value_type === SettingType::INTEGER->value)
                    <flux:input wire:model.live.blur="form.value" type="number" step="1"/>
                @elseif($this->form->value_type === SettingType::DOUBLE->value)
                    <flux:input wire:model.live.blur="form.value" type="number" step="any"/>

                @else
                    <flux:input wire:model.live.blur="form.value" type="text"/>
                @endif

                <flux:error name="form.value"/>
            </flux:field>

            <flux:field class="col-span-full">
                <flux:label>{{ __('common.description') }}</flux:label>
                <flux:input wire:model.live.blur="form.description" type="text"/>
                <flux:error name="form.description"/>
            </flux:field>

            <div class="col-span-full">
                <div class="flex flex-col md:flex-row md:justify-between gap-2">
                    @if($this->form->setting)
                        @canany(['sys.admin', 'setting.delete'])
                            <flux:button type="button" variant="primary"
                                         color="red"
                                         wire:click="delete"
                                         class="w-full md:w-auto"
                                         wire:loading.attr="disabled"
                                         icon="trash"
                                         wire:target="delete, save">
                                {{  __('common.delete') }}
                            </flux:button>
                        @endcanany
                    @endif

                    @canany(['sys.admin', 'setting.create', 'setting.update'])
                        <flux:button type="submit" variant="primary"
                                     class="w-full md:w-auto md:ml-auto"
                                     wire:loading.attr="disabled"
                                     wire:target="delete, save">
                            {{ $this->form->setting ? __('common.update') : __('common.save') }}
                        </flux:button>
                    @endcanany
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
