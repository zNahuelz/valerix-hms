<?php

use Livewire\Component;

new class extends Component {
    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('medicine.create')])
            ->title(__('views.medicine.create'));
    }
};
?>

<div>
    <h1>Medicine Index Wip!</h1>
</div>