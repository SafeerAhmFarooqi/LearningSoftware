<?php
  namespace App\Traits;

  trait ManagesProperties
  {
    public $inputAttributes = [];
    public $picker = 'single'; //single, range
    public $multiSelect = false; //single, range
    public $initialMonthYear = '';
    public $livewireHoverData = [];
    public $preOccupiedDates = [];
    public $selectedDates = [];
    public $disableDates = [];
  }
  