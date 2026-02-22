@props([
    'paginator' => null,
])

@php
    $simple = ! $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
@endphp

@if ($simple)
    {{-- Simple Pagination (Previous/Next only) --}}
    <div {{ $attributes->class('pt-3 border-t border-zinc-100 dark:border-zinc-700 flex justify-between items-center') }} data-flux-pagination>
        <div></div>
        @if ($paginator->hasPages())
            <div class="flex items-center bg-white border border-zinc-200 rounded-[8px] p-[1px] dark:bg-white/10 dark:border-white/10">
                <button type="button" @disabled($paginator->onFirstPage()) wire:click="previousPage('{{ $paginator->getPageName() }}')" class="flex justify-center items-center size-8 sm:size-6 rounded-[6px] text-zinc-400 disabled:text-zinc-200 dark:text-white dark:disabled:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-white/20">
                    <flux:icon.chevron-left variant="micro" />
                </button>

                <button type="button" @disabled(! $paginator->hasMorePages()) wire:click="nextPage('{{ $paginator->getPageName() }}')" class="flex justify-center items-center size-8 sm:size-6 rounded-[6px] text-zinc-400 disabled:text-zinc-200 dark:text-white dark:disabled:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-white/20">
                    <flux:icon.chevron-right variant="micro" />
                </button>
            </div>
        @endif
    </div>
@else
    {{-- Full Pagination --}}
    <div {{ $attributes->class('@container pt-3 border-t border-zinc-100 dark:border-zinc-700 flex justify-between items-center gap-3') }} data-flux-pagination>
        @if ($paginator->total() > 0)
            <div class="text-zinc-500 dark:text-zinc-400 text-xs font-medium whitespace-nowrap">
                {{ __('pagination.showing') }} {{ $paginator->firstItem() }} 
                {{ __('pagination.to') }} {{ $paginator->lastItem() }} 
                {{ __('pagination.of') }} {{ $paginator->total() }} 
                {{ __('pagination.results') }}
            </div>
        @else
            <div></div>
        @endif

        @if ($paginator->hasPages())
            {{-- Mobile: Only Chevrons (Hidden at 40rem container width) --}}
            <div class="flex @[40rem]:hidden items-center bg-white border border-zinc-200 rounded-[8px] p-[1px] dark:bg-white/10 dark:border-white/10">
                <button type="button" @disabled($paginator->onFirstPage()) wire:click="previousPage('{{ $paginator->getPageName() }}')" class="flex justify-center items-center size-8 rounded-[6px] text-zinc-400 disabled:text-zinc-200 dark:text-white dark:disabled:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-white/20">
                    <flux:icon.chevron-left variant="micro" />
                </button>

                <button type="button" @disabled(! $paginator->hasMorePages()) wire:click="nextPage('{{ $paginator->getPageName() }}')" class="flex justify-center items-center size-8 rounded-[6px] text-zinc-400 disabled:text-zinc-200 dark:text-white dark:disabled:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-white/20">
                    <flux:icon.chevron-right variant="micro" />
                </button>
            </div>

            <div class="hidden @[40rem]:flex items-center bg-white border border-zinc-200 rounded-[8px] p-[1px] dark:bg-white/10 dark:border-white/10">
                <button type="button" @disabled($paginator->onFirstPage()) wire:click="previousPage('{{ $paginator->getPageName() }}')" aria-label="{{ __('pagination.previous') }}" class="flex justify-center items-center size-6 rounded-[6px] text-zinc-400 disabled:text-zinc-200 dark:text-white dark:disabled:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-white/20">
                    <flux:icon.chevron-left variant="micro" />
                </button>

                @foreach (\Livewire\invade($paginator)->elements() as $element)
                    @if (is_string($element))
                        <div class="cursor-default flex justify-center items-center text-xs size-6 rounded-[6px] font-medium text-zinc-400">{{ $element }}</div>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <div class="cursor-default flex justify-center items-center text-xs h-6 px-2 rounded-[6px] font-medium dark:text-white text-zinc-800 bg-zinc-100 dark:bg-white/10">{{ $page }}</div>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="text-xs h-6 px-2 rounded-[6px] text-zinc-400 font-medium hover:bg-zinc-100 dark:hover:bg-white/20 hover:text-zinc-800 dark:hover:text-white">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <button type="button" @disabled(! $paginator->hasMorePages()) wire:click="nextPage('{{ $paginator->getPageName() }}')" aria-label="{{ __('pagination.next') }}" class="flex justify-center items-center size-6 rounded-[6px] text-zinc-400 disabled:text-zinc-200 dark:text-white dark:disabled:text-zinc-600 hover:bg-zinc-100 dark:hover:bg-white/20">
                    <flux:icon.chevron-right variant="micro" />
                </button>
            </div>
        @endif
    </div>
@endif