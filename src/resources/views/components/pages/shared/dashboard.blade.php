<?php

use Livewire\Component;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts::dashboard')->title(__('views.dashboard'));
    }
};
?>

<div>
    <h1>Hola mundo!!!!!!</h1>
</div>