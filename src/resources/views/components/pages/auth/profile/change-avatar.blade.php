<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

new class extends Component {
    use WithFileUploads;

    public $avatar = null;
    public bool $isSubmitting = false;

    protected function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function messages(): array
    {
        return [
            'avatar.required' => __('validation.avatar.required'),
            'avatar.image' => __('validation.avatar.image'),
            'avatar.mimes' => __('validation.avatar.mimes'),
            'avatar.max' => __('validation.avatar.max', ['max' => '2MB']),
        ];
    }

    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->dispatch('lock-area');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->isSubmitting = false;
            $this->dispatch('unlock-area');
            throw $e;
        }

        try {
            $user = Auth::user();

            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $this->avatar->store('avatars', 'public');

            $user->forceFill(['avatar' => $path])->save();

            $this->avatar = null;
            $this->isSubmitting = false;
            $this->dispatch('unlock-area');
            Session::flash('info', __('auth.avatar_updated'));
            $this->redirectRoute('dashboard');

        } catch (Exception $e) {
            $this->isSubmitting = false;
            $this->dispatch('unlock-area');
            $this->addError('avatar', __('auth.errors.change_avatar_failed'));
        }
    }
};
?>

<div>
    <div class="flex justify-center px-4">
        <div class="w-md mt-2">
            <flux:fieldset>
                <div class="flex justify-center mb-4">
                    @if(Auth::user()->avatar && !$avatar)
                        <img
                            src="{{ Storage::url(Auth::user()->avatar) }}"
                            alt="avatar"
                            class="w-24 h-24 rounded-full object-cover border-2 border-zinc-200 dark:border-zinc-700"
                        />
                    @else
                        @if(!$avatar)
                            <div
                                class="w-24 h-24 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                <flux:icon name="user" class="w-12 h-12 text-zinc-400"/>
                            </div>
                        @endif
                    @endif
                </div>
                @if($avatar && $avatar->isPreviewable())
                    <div class="flex flex-col items-center gap-2 mb-4">
                        <p class="text-sm text-zinc-500">{{ __('auth.avatar_preview') }}</p>
                        <img
                            src="{{ $avatar->temporaryUrl() }}"
                            alt="preview"
                            class="w-24 h-24 rounded-full object-cover border-2 border-blue-400"
                        />
                    </div>
                @endif
                <div class="grid grid-cols-1 gap-2">
                    <flux:field>
                        <input
                            type="file"
                            wire:model="avatar"
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-sm text-zinc-500
                                   file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:border-0
                                   file:text-sm file:font-medium
                                   file:bg-zinc-100 file:text-zinc-700
                                   dark:file:bg-zinc-700 dark:file:text-white"
                        />
                        <flux:error name="avatar"/>
                    </flux:field>
                </div>
                <div class="flex justify-start mt-4">
                    <flux:button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        :disabled="!$avatar"
                        variant="primary"
                        icon="photo"
                    >
                        <span wire:loading.remove wire:target="submit">{{ __('common.update') }}</span>
                        <span wire:loading wire:target="submit">{{ __('common.loading') }}</span>
                    </flux:button>
                </div>
            </flux:fieldset>
        </div>
    </div>
</div>
