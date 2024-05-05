<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\Initializer;
use App\Traits\ManagesProperties;

class DateRangePicker extends Component
{
    use Initializer;
    use ManagesProperties;

    public function mount(){
       $this->initialize();
    }

    public function render()
    {
        return view('livewire.date-range-picker');
    }
}
