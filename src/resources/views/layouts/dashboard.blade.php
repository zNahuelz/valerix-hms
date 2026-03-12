<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
<flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.header>
        <flux:sidebar.brand href="#" logo="{{ asset('images/app-icon.png') }}"
                            logo:dark="{{ asset('images/app-icon.png') }}" icon="beaker"
                            name="{{ substr(auth()->user()->clinic->name, 10) }}"/>
        <flux:sidebar.collapse
            class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"/>
    </flux:sidebar.header>
    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="{{ route('dashboard') }}" wire:navigate>{{ __('common.home') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="inbox" badge="12" href="#">Inbox</flux:sidebar.item>
        @canany(['sys.admin', 'clinic.index', 'clinic.create', 'clinic.update', 'clinic.delete', 'clinic.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('clinic.*')" persist icon="building-office-2"
                                heading="{{ trans_choice('clinic.clinic', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'clinic.create'])
                    <flux:sidebar.item href="{{ route('clinic.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'clinic.index'])
                    <flux:sidebar.item href="{{ route('clinic.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'doctor.index', 'doctor.create', 'doctor.update', 'doctor.delete', 'doctor.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('doctor.*')" persist icon="hand-raised"
                                heading="{{ trans_choice('doctor.doctor', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'doctor.create'])
                    <flux:sidebar.item href="{{ route('doctor.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                    @canany(['sys.admin', 'doctor.create.unavailabilities'])
                        <flux:sidebar.item href="{{ route('doctor.create.unavailabilities') }}"
                                           wire:navigate>{{ __('common.store') . ' ' . trans_choice('common.unavailability',1) }}
                        </flux:sidebar.item>
                    @endcanany
                @canany(['sys.admin', 'doctor.index'])
                    <flux:sidebar.item href="{{ route('doctor.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'worker.index', 'worker.create', 'worker.update', 'worker.delete', 'worker.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('worker.*')" persist icon="lifebuoy"
                                heading="{{ trans_choice('worker.worker', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'worker.create'])
                    <flux:sidebar.item href="{{ route('worker.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'worker.index'])
                    <flux:sidebar.item href="{{ route('worker.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'nurse.index', 'nurse.create', 'nurse.update', 'nurse.delete', 'nurse.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('nurse.*')" persist icon="heart"
                                heading="{{ trans_choice('nurse.nurse', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'nurse.create'])
                    <flux:sidebar.item href="{{ route('nurse.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'nurse.index'])
                    <flux:sidebar.item href="{{ route('nurse.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'medicine.index', 'medicine.create', 'medicine.update', 'medicine.delete', 'medicine.restore',
         'presentation.index', 'presentation.create', 'presentation.update', 'presentation.delete', 'presentation.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('medicine.*','presentation.*')" icon="beaker"
                                heading="{{ trans_choice('medicine.medicine', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'medicine.create'])
                    <flux:sidebar.item href="{{ route('medicine.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'medicine.index'])
                    <flux:sidebar.item href="{{ route('medicine.index') }}"
                                       wire:navigate>{{ __('common.index') }}</flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'presentation.index'])
                    <flux:sidebar.item href="{{ route('presentation.index') }}"
                                       wire:navigate>{{ trans_choice('presentation.presentation', 2) }}</flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'clinicMedicine.index', 'clinicMedicine.create', 'clinicMedicine.update', 'clinicMedicine.delete', 'clinicMedicine.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('clinicMedicine.*')" icon="cube"
                                heading="{{ trans_choice('clinic-medicine.clinic_medicine', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'clinicMedicine.create'])
                    <flux:sidebar.item href="{{ route('clinicMedicine.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'clinicMedicine.index'])
                    <flux:sidebar.item href="{{ route('clinicMedicine.index') }}"
                                       wire:navigate>{{ __('common.index') }}</flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'buyOrder.index', 'buyOrder.create', 'buyOrder.update', 'buyOrder.delete', 'buyOrder.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('buy-order.*')" icon="archive-box"
                                heading="{{ trans_choice('buy-order.buy_order', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'buyOrder.create'])
                    <flux:sidebar.item href="{{ route('buyOrder.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'buyOrder.index'])
                    <flux:sidebar.item href="{{ route('buyOrder.index') }}"
                                       wire:navigate>{{ __('common.index') }}</flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'patient.index', 'patient.create', 'patient.update', 'patient.delete', 'patient.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('patient.*')" persist icon="users"
                                heading="{{ trans_choice('patient.patient', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'patient.create'])
                    <flux:sidebar.item href="{{ route('patient.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'patient.index'])
                    <flux:sidebar.item href="{{ route('patient.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'supplier.index', 'supplier.create', 'supplier.update', 'supplier.delete', 'supplier.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('supplier.*')" persist icon="globe-americas"
                                heading="{{ trans_choice('supplier.supplier', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'supplier.create'])
                    <flux:sidebar.item href="{{ route('supplier.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'supplier.index'])
                    <flux:sidebar.item href="{{ route('supplier.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'treatment.index', 'treatment.create', 'treatment.update', 'treatment.delete', 'treatment.restore'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('treatment.*')" persist icon="swatch"
                                heading="{{ trans_choice('treatment.treatment', 2) }}"
                                class="grid">
                @canany(['sys.admin', 'treatment.create'])
                    <flux:sidebar.item href="{{ route('treatment.create') }}" wire:navigate>{{ __('common.store') }}
                    </flux:sidebar.item>
                @endcanany
                @canany(['sys.admin', 'treatment.index'])
                    <flux:sidebar.item href="{{ route('treatment.index') }}" wire:navigate>{{ __('common.index') }}
                    </flux:sidebar.item>
                @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin', 'holiday.index', 'holiday.create', 'holiday.update', 'holiday.delete'])
            <flux:sidebar.group expandable :expanded="request()->routeIs('holiday.*') || request()->routeIs('system.*')" persist icon="circle-stack"
                                heading="{{ __('common.system') }}"
                                class="grid">
                @canany(['sys.admin', 'holiday.index'])
                    <flux:sidebar.item href="{{ route('holiday.index') }}" wire:navigate>{{ trans_choice('common.holiday',2) }}
                    </flux:sidebar.item>
                @endcanany
                    @canany(['sys.admin', 'voucherType.index'])
                        <flux:sidebar.item href="{{ route('voucherType.index') }}" wire:navigate>{{ trans_choice('voucher-type.voucher_type',2) }}
                        </flux:sidebar.item>
                    @endcanany
                    @canany(['sys.admin', 'voucherSerie.index'])
                        <flux:sidebar.item href="{{ route('voucherSerie.index') }}" wire:navigate>{{ trans_choice('voucher-serie.voucher_serie',2) }}
                        </flux:sidebar.item>
                    @endcanany
                    @canany(['sys.admin', 'paymentType.index'])
                        <flux:sidebar.item href="{{ route('paymentType.index') }}" wire:navigate>{{ trans_choice('payment-type.payment_type',2) }}
                        </flux:sidebar.item>
                    @endcanany
            </flux:sidebar.group>
        @endcanany

        @canany(['sys.admin','setting.index'])
        <flux:sidebar.item icon="cog" href="{{route('setting.index')}}">{{__('common.settings')}}</flux:sidebar.item>
        @endcanany
    </flux:sidebar.nav>
    <flux:sidebar.spacer/>
    <flux:dropdown position="top" align="start">
        <flux:sidebar.item icon="computer-desktop">
            {{ __('common.toggle_theme') }}
        </flux:sidebar.item>
        <flux:menu>
            <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">
                {{ __('common.light') }}
            </flux:menu.item>
            <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">
                {{ __('common.dark') }}
            </flux:menu.item>
            <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">
                {{ __('common.system') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:sidebar.profile avatar="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : null }}" name="{{ auth()->user()->username }}"/>
        <flux:menu>
            <flux:menu.item icon="user-circle" href="{{route('profile.view')}}">{{ __('auth.profile') }}</flux:menu.item>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                    {{ __('auth.logout') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>
    <flux:spacer/>
    <flux:dropdown position="top" align="start">
        <flux:profile avatar="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : null }}" name="{{ auth()->user()->username }}"/>
        <flux:menu>
            <flux:menu.item icon="user-circle" href="{{route('profile.view')}}">{{ __('auth.profile') }}</flux:menu.item>
            <flux:menu.separator/>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                    {{ __('auth.logout') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>
<flux:main>
    @foreach (['success', 'error', 'warning', 'info'] as $type)
        @if (session()->has($type))
            <x-shared.alert :type="$type">
                {{ session($type) }}
            </x-shared.alert>
        @endif
    @endforeach
    @if(isset($heading))
        <flux:heading size="xl" level="1" class="mb-4">
            {{ $heading }}
        </flux:heading>
    @endif
    <flux:separator variant="subtle"/>
    <div class="p-3">
        {{ $slot ?? '' }}
        @yield('content')
    </div>
        @livewireScripts
        @fluxScripts
</flux:main>
</body>
</html>
