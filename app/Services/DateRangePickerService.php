<?php

namespace App\Services;

use Exception;

class DateRangePickerService {
    private $selectedRawDateString = '';
    private $selectedRawDateCarbon;


    public function __construct($inputDateString = '',$toFormat = '',$fromFormat = '')
    {
        $this->setInitialDate($inputDateString = '',$toFormat = '',$fromFormat = '');
    }

    public function setInitialDate($inputDateString = '',$toFormat = '',$fromFormat = ''){
        if ($inputDateString) {
            $this->validateSetInitialDate();
        } else {
            # code...
        }
        
    }

    public function validateSetInitialDate($inputDateString = '',$toFormat = '',$fromFormat = ''){
        if (!is_string($inputDateString)) {
            throw new Exception("Please input date in string format");
        }

        if (!$toFormat || !$fromFormat) {
            throw new Exception("To format and From Format are both required");
        }
    }

    public function getRawDateString(){
        return (string)$this->selectedRawDateString;
    }

}