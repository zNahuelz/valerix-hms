<?php

use Livewire\Component;
use App\Models\User;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use App\Notifications\EmailUpdatedNotification;

new class extends Component {

    public UserForm $form;

    public function mount(string $userId): void
    {
        if (!is_numeric($userId)) {
            $this->redirectWithError($userId);
            return;
        }

        $user = User::withTrashed()->find((int)$userId);

        if (!$user) {
            $this->redirectWithError($userId);
            return;
        }

        $this->form->user = $user;
        $this->form->fill($user->toArray());
    }

    protected function redirectWithError(string $userId): void
    {
        Session::flash('error', __('user.errors.not_found', ['id' => $userId]));
        $this->redirectRoute('user.index');
    }

    public function save()
    {
        $sanitized = $this->form->sanitized();
        $this->validate();
        try {
            $emailChanged = $this->form->user->email !== $sanitized['email'];

            $this->form->user->update($sanitized);

            if ($emailChanged) {
                $this->form->user->notify(new EmailUpdatedNotification(Carbon::now('America/Lima')->format('d/m/Y h:i:s A')));
            }

            Session::flash('success', __('user.updated', [
                'username' => $sanitized['username'],
                'id' => $this->form->user->id,
            ]));
        } catch (Exception) {
            Session::flash('error', __('user.errors.update_failed'));
        }
        $this->redirectRoute('user.index');
    }

    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('user.edit')])
            ->title(__('views.user.edit'));
    }
};
?>

<div class="flex justify-center px-4">
    <form class="w-full md:max-w-md" wire:submit="save">
        <x-shared.alert
            type="info">{{__('user.editing_user', ['username' => $this->form->user?->username, 'email' => $this->form->user?->email])}}</x-shared.alert>
        @if($this->form->user && $this->form->user->trashed())
            <x-shared.alert type="info">{{ __('user.is_deleted') }}</x-shared.alert>
        @endif
        <flux:fieldset class="grid grid-cols-1 gap-3"
                       wire:loading.attr="disabled"
                       wire:target="save">
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.username') }}</flux:label>
                <flux:input wire:model.live.blur="form.username" type="text"/>
                <flux:error name="form.username"/>
            </flux:field>
            <flux:field>
                <flux:label badge="{{ __('common.required') }}">{{ __('common.email') }}</flux:label>
                <flux:input wire:model.live.blur="form.email" type="email"/>
                <flux:error name="form.email"/>
            </flux:field>
            <div class="col-span-full">
                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" class="w-full md:w-auto"
                                 wire:loading.attr="disabled"
                                 wire:target="save">
                        {{ __('common.update') }}
                    </flux:button>
                </div>
            </div>
        </flux:fieldset>
    </form>
</div>
