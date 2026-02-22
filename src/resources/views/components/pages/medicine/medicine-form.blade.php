<?php

use Livewire\Component;

new class extends Component {
    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('medicine.index')])
            ->title(__('views.medicine.index'));
    }
};
?>

<div>
    <h1>Medicine Form Wip!</h1>
</div>