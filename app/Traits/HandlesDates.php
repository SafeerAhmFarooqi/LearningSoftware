<?php
  namespace App\Traits;

  trait HandlesDates
  {
    public function initializeDates(){
      $this->initializeInitialMonthYear();
    }

    public function initializeInitialMonthYear(){
      // Ensure the format is Y-m and validate the year and month
      if (preg_match('/^(\d{4})-(\d{1,2})$/', $this->initialMonthYear, $matches)) {
        $year = (int) $matches[1];
        $month = (int) $matches[2];
  
        // Validate year range
        if ($year < 1900 || $year > 2300) {
            $year = now()->year; // Default to the current year
        }
  
        // Validate month range
        if ($month < 1 || $month > 12) {
            $month = 1; // Default to January
        }
  
        $this->initialMonthYear = sprintf('%04d-%02d', $year, $month);
      } else {
          // Default to the current year and month
          $this->initialMonthYear = now()->format('Y-m');
      }
    }
  }
  