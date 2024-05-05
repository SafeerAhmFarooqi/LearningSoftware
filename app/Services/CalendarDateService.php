<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;

class CalendarDateService {
    private $ranges = [];
    private $fromFormat;
    private $toFormat;
    private $validDates = [
        'Y-m-d' => '2000-01-01',
        'm/d/Y' => '01/01/2000',
        'd-m-Y' => '01-01-2000',
        'Y/m/d' => '2000/01/01',
        'd/m/Y' => '01/01/2000',
        'Y.m.d' => '2000.01.01',
        'd.m.Y' => '01.01.2000',
        'Ymd'   => '20000101',  
        'mdY'   => '01012000',  
        'dmY'   => '01012000',
        'd.m.Y H:i:s' => '01.01.2000 00:00:00',
    ];

    public function __construct($ranges = [], $fromFormat = 'Y-m-d', $toFormat = 'Y-m-d'){
        $this->setFromFormat($fromFormat);
        $this->setToFormat($toFormat);
        $this->setRange($ranges);
    }

    public function setFromFormat($fromFormat){
        $this->validateDateFormat($fromFormat,'from format');
        $this->fromFormat = $fromFormat;
    }

    public function setToFormat($toFormat){
        $this->validateDateFormat($toFormat,'to format');
        $this->toFormat = $toFormat;
    }

    public function setRange($ranges){
        $this->validateRanges($ranges);
    }

    public function validateDateFormat($format,$type){
        if (!array_key_exists($format,$this->validDates)) {
            throw new InvalidArgumentException("Invalid ".$type." : ".$format);
        }
    }

    public function validateRanges($ranges){
        if (!is_array($ranges)) {
            throw new InvalidArgumentException("Array Expected As Argument");
        }

        foreach ($ranges as $key => $range) {
            
        }
    }
}