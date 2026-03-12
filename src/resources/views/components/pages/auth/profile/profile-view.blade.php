<?php

use Livewire\Component;
use Illuminate\Support\Carbon;

new class extends Component {
    public string $area = 'index';
    public bool $isLocked = false;
    public string $username = '';
    public string $userType = '';
    public ?array $profileInfo = null;

    public function mount(): void
    {
        $user = auth()->user()->load(['doctor', 'nurse', 'worker']);

        $this->username = $user->username;
        $this->userType = mb_strtolower($user->roles[0]['name']) ?? '----';

        $this->profileInfo = match ($this->userType) {
            'administrador' => $user->worker?->toArray(),
            'doctor' => $user->doctor?->toArray(),
            'enfermera' => $user->nurse?->toArray(),
            'secretaria' => $user->worker?->toArray(),
            default => null,
        };
    }

    protected $listeners = [
        'lock-area' => 'lockArea',
        'unlock-area' => 'unlockArea'
    ];

    public function lockArea(): void
    {
        $this->isLocked = true;
    }

    public function unlockArea(): void
    {
        $this->isLocked = false;
    }


    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('auth.profile')])
            ->title(__('views.auth.profile'));
    }
};
?>

<div>
    <flux:radio.group wire:model.live="area" variant="segmented" :disabled="$isLocked"
                      class=" max-sm:w-full max-sm:flex-col max-sm:h-full max-sm:p-2">
        <flux:radio label="{{__('auth.profile')}}" value="index" icon="user" class="max-sm:p-2"/>
        <flux:radio label="{{__('auth.change_password')}}" value="changePassword" icon="key" class="max-sm:p-2"/>
        <flux:radio label="{{__('auth.change_avatar')}}" value="changeAvatar" icon="photo" class="max-sm:p-2"/>
    </flux:radio.group>
    @if($area === 'index')
        <div class="flex justify-center px-4">
            <div class="w-4xl mt-2">
                <flux:fieldset class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <flux:field>
                        <flux:label>{{ __('auth.username') }}</flux:label>
                        <flux:input readonly value="{{ $username }}" icon="user" type="text"/>
                    </flux:field>
                    @if($profileInfo)
                        <flux:field>
                            <flux:label>{{ trans_choice('common.name',2) }}</flux:label>
                            <flux:input readonly value="{{ $profileInfo['names'] ?? __('common.null')}}" icon="user"
                                        type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.paternal_surname_alt') }}</flux:label>
                            <flux:input readonly value="{{ $profileInfo['paternal_surname'] ?? __('common.null')}}"
                                        icon="user" type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.maternal_surname_alt') }}</flux:label>
                            <flux:input readonly value="{{ $profileInfo['maternal_surname'] ?? __('common.null')}}"
                                        icon="user" type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.dni') }}</flux:label>
                            <flux:input readonly value="{{ $profileInfo['dni'] ?? __('common.null') }}"
                                        icon="identification"
                                        type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.phone') }}</flux:label>
                            <flux:input readonly value="{{ $profileInfo['phone'] ?? __('common.null')}}" icon="phone"
                                        type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.address') }}</flux:label>
                            <flux:input readonly value="{{ $profileInfo['address'] ?? __('common.null') }}"
                                        icon="map-pin"
                                        type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.hired_at') }}</flux:label>
                            <flux:input readonly
                                        value="{{ Carbon::parse($profileInfo['hired_at'])->format('d/m/Y') ?? __('common.null') }}"
                                        icon="calendar-days" type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('common.created_at') }}</flux:label>
                            <flux:input readonly
                                        value="{{  Carbon::parse($profileInfo['created_at'])->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                                        icon="calendar-days" type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('auth.last_account_change') }}</flux:label>
                            <flux:input readonly
                                        value="{{ Carbon::parse($profileInfo['updated_at'])->timezone('America/Lima')->format('d/m/Y g:i A') ?? __('common.null') }}"
                                        icon="calendar-days" type="text"/>
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ trans_choice('auth.role',1) }}</flux:label>
                            <flux:input readonly value="{{ strtoupper($this->userType) }}" icon="shield-check"
                                        type="text"/>
                        </flux:field>
                        @if(isset($profileInfo['position']))
                            <flux:field>
                                <flux:label>{{ __('common.position') }}</flux:label>
                                <flux:input readonly value="{{ strtoupper($profileInfo['position']) }}"
                                            icon="shield-check" type="text"/>
                            </flux:field>
                        @endif
                        <!-----TODO:: Show permissions table.----->
                    @endif


                </flux:fieldset>
            </div>
        </div>
    @endif
    @if($area === 'changePassword')
        <livewire:pages.auth.profile.change-password></livewire:pages.auth.profile.change-password>
    @endif
    @if($area === 'changeAvatar')
        <livewire:pages.auth.profile.change-avatar></livewire:pages.auth.profile.change-avatar>
    @endif
</div>
