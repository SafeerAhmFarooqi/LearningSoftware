<?php
  namespace App\Traits;

  trait ManagesProperties
  {
    public $inputAttributes = [];
    public $picker = 'single'; //single, range
    public $selectedDate = '';
    public $selectedDates = [];
  }
  