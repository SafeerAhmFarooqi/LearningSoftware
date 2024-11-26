<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\Initializer;
use App\Traits\ManagesProperties;
use App\Traits\HandlesDates;

class DateRangePicker extends Component
{
    use Initializer;
    use ManagesProperties;
    use HandlesDates;

    public function mount(){
       $this->initialize();
       $this->getLivewireHoverData($this->initialMonthYear);
    }
    
    public function getLivewireHoverData($currentMonthYear){
        $this->livewireHoverData = [
            1 => "First Hover Data for the month {$currentMonthYear}",
            2 => "Second Hover Data for the month {$currentMonthYear}",
        ];
    }
    public function render()
    {
        return view('livewire.date-range-picker');
    }
}
