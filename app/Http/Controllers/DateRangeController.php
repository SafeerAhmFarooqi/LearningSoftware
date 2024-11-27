<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DateRangeController extends MasterController
{
    public function submit(Request $request)
    {
        // Auto-detect and process the input
        $dates = $this->processInput($request->input('selectedRanges', $request->input('selectedDates', '')));

        //For picker type set to single and multi select set to true or false 
        foreach ($dates as $date) {
            Log::info($date);
        }

        //For picker type set to single and multi select set to false
        Log::info($date[0]);

        //For picker type set to range and multi select set to true or false
        foreach ($dates as $range) {
            foreach ($range as $date) {
                Log::info($date);
            }
        }

        //For picker type set to range and multi select set to false
        foreach ($dates[0] as $date) {
            Log::info($date);
        }

        return response()->json($dates);
    }

    private function processInput($input)
    {
        $input = trim($input);

        // Check if input is empty
        if (empty($input)) {
            return [];
        }

        // Single date case (string format)
        if ($this->isSingleDate($input)) {
            return [$this->parseDate($input)];
        }

        // Flat array of multiple dates (comma-separated)
        if ($this->isFlatArrayOfDates($input)) {
            return $this->processFlatDates($input);
        }

        // Ranges of dates (enclosed in multiple brackets)
        if ($this->isArrayOfRanges($input)) {
            return $this->processDateRanges($input);
        }

        // Fallback: return empty
        return [];
    }

    private function isSingleDate($input)
    {
        return Carbon::hasFormat(trim($input), 'Y-m-d');
    }

    private function isFlatArrayOfDates($input)
    {
        // Check if the input matches the format of a single array of dates
        if (preg_match('/^[\d,\- ]+$/', $input)) { // Escape the hyphen
            $dates = explode(',', $input);
            foreach ($dates as $date) {
                if (!Carbon::hasFormat(trim($date), 'Y-m-d')) {
                    return false;
                }
            }
            return true;
        }
    
        return false;
    }

    private function processFlatDates($input)
    {
        // Convert the flat array of dates to a PHP array
        return array_map(
            fn($date) => Carbon::parse(trim($date))->format('Y-m-d'),
            explode(',', $input)
        );
    }

    private function isArrayOfRanges($input)
    {
        // Check if the input contains multiple date ranges enclosed in brackets
        return preg_match_all('/\[(.*?)\]/', $input);
    }

    private function processDateRanges($input)
    {
        $ranges = [];

        // Match each range: [startDate, endDate]
        preg_match_all('/\[(.*?)\]/', $input, $matches);

        foreach ($matches[1] as $range) {
            $rangeDates = explode(',', $range);
            $ranges[] = $this->expandRange($rangeDates);
        }

        return $ranges;
    }

    private function expandRange($rangeDates)
    {
        $dates = [];

        foreach ($rangeDates as $date) {
            $dates[] = Carbon::parse(trim($date))->format('Y-m-d');
        }

        return $dates;
    }

    private function parseDate($date)
    {
        return Carbon::parse(trim($date))->format('Y-m-d');
    }
}
