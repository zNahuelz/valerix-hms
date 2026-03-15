<?php

use Livewire\Component;

new class extends Component {
    public string $token = '';
    public string $email = '';

    public function mount(): void
    {
        $this->token = request()->route('token', '');
        $this->email = request()->query('email', '');
    }

    public function render()
    {
        return $this->view()->layout('layouts::guest')->title(__('views.password-recovery'));
    }
};
?>

<div>
    @if (blank($token))
        <livewire:pages.auth.password-recovery.send-recovery-email/>
    @else
        <livewire:pages.auth.password-recovery.change-password-with-token
            :token="$token"
            :email="$email"
        />
    @endif
</div>
