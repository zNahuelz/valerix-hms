<?php

use Livewire\Component;

new class extends Component
{
    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('clinic.detail')])
            ->title(__('views.clinic.detail'));
    }
};
?>

<div>
    <h1>Clinic Detail Wip!</h1>
</div>
