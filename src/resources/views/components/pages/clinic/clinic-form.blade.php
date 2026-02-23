<?php

use Livewire\Component;

new class extends Component
{
    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('clinic.create')])
            ->title(__('views.clinic.create'));
    }
};
?>

<div>
    <h1>Clinic Form Wip!</h1>
</div>
