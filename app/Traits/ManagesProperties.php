<?php
  namespace App\Traits;

  trait ManagesProperties
  {
    public $inputAttributes = [];
    public $selector = 'single'; //single, range, optional
    public $multipleSelection = false;
    public $selectedDates = [];
  }
  