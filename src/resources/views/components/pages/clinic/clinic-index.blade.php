<?php

use Livewire\Component;

new class extends Component
{
    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('clinic.index')])
            ->title(__('views.clinic.index'));
    }
};
?>

<div>
   <h1>Clinic Index Wip!</h1>
</div>
