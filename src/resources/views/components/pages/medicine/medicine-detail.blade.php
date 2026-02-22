<?php

use Livewire\Component;

new class extends Component {
    public function render(): mixed
    {
        return $this->view()
            ->layout('layouts::dashboard', ['heading' => __('medicine.detail')])
            ->title(__('views.medicine.detail'));
    }
};
?>

<div>
    <h1>Medicine Detail Wip!!</h1>
</div>