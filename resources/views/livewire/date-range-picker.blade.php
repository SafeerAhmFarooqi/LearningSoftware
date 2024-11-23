<div>
    <div class="calendar-container" 
         x-data="calendarApp({{ json_encode($multiSelect) }}, '{{ $picker }}', {{ json_encode($preOccupiedDates) }}, {{ json_encode($disableDates) }})" 
         x-init="generateCalendar()">
       <!-- Dynamic Input Field -->
       <input 
        wire:model="selectedDates"
        type="text"
        x-bind:value="picker === 'range' 
                        ? selectedRanges.map(range => `[${range.join(', ')}]`).join(', ') 
                        : selectedDates.join(', ')"
        x-bind:name="picker === 'range' ? 'selectedRanges' : 'selectedDates'"
        @foreach ($inputAttributes as $keyAttribute => $inputAttribute)
            @if (!is_string($inputAttribute)) @continue @endif
            {{ ' ' }}{{ $keyAttribute }}="{{ e($inputAttribute) }}"
        @endforeach
        @click="open = !open; console.log('Open state:', open)"
        readonly
        aria-label="Selected Dates"
   />

       <!-- Calendar -->
       <div class="calendar" x-show="open" x-transition x-cloak @click.outside="handleCalendarHide()">
           <div class="navigation">
               <button type="button" @click="prevMonth()"> &lt; Prev </button>
               <div class="month">
                   <select x-model="currentMonth" @change="generateCalendar()">
                       <template x-for="(month, index) in months">
                           <option :value="index" x-text="month"></option>
                       </template>
                   </select>
               </div>
               <div class="year">
                   <input type="number" x-model="currentYear" @input="generateCalendar()" placeholder="Year" min="1900" max="2100">
               </div>
               <button type="button" @click="nextMonth()"> Next &gt; </button>
           </div>

           <div class="days">
               <!-- Weekday Headers -->
               <template x-for="day in weekdays">
                   <div class="day header" x-text="day"></div>
               </template>

               <!-- Dynamic Calendar Days -->
               <template x-for="day in calendarDays">
                <div class="day"
                     :class="{
                         'empty': day.empty, 
                         'selected': isSelected(day.date),
                         'pre-occupied': preOccupiedDates.includes(day.date),
                         'disabled': disableDates.includes(day.date)
                     }"
                     @click="handleDateClick(day.date, day.disabled, day.preOccupied)">
                    <span x-text="day.label"></span>
                </div>
            </template>
            
           </div>
       </div>
    </div>
</div>




@assets
<script>
   function calendarApp(multiSelect = false, picker = 'single', preOccupiedDates = [], disableDates = []) {
    return {
        open: false,
        multiSelect: multiSelect === true || multiSelect === 'true', // Ensure boolean
        picker,
        preOccupiedDates: generateDateRange(preOccupiedDates),
        disableDates: generateDateRange(disableDates),
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        selectedDates: [], // To store dates for 'single' mode
        selectedRanges: [], // To store ranges for 'range' mode
        calendarDays: [],
        months: [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ],
        weekdays: ["S", "M", "T", "W", "T", "F", "S"],

        generateCalendar() {
            const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();

            const days = [];
            for (let i = 0; i < firstDay; i++) {
                days.push({ label: "", empty: true });
            }
            for (let i = 1; i <= daysInMonth; i++) {
                const date = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                days.push({
                    label: i,
                    date,
                    empty: false,
                    preOccupied: this.preOccupiedDates.includes(date),
                    disabled: this.disableDates.includes(date),
                });
            }
            this.calendarDays = days;
        },
        handleCalendarHide() {
    this.open = false;

    if (this.picker === 'range') {
        if (!this.multiSelect) {
            // Single range selection: handle incomplete ranges
            if (this.selectedRanges.length === 1) {
                const lastRange = this.selectedRanges[0];
                if (lastRange.length < 2) {
                    // Clear incomplete range
                    this.selectedRanges = [];
                    console.log('Cleared incomplete range on hide');
                }
            }
        } else {
            // Multi-range selection: Ensure no incomplete ranges
            this.selectedRanges = this.selectedRanges.filter(range => range.length >= 2);
            console.log('Filtered incomplete ranges for multi-select on hide');
        }
    } else if (this.picker === 'single') {
        if (!this.multiSelect && this.selectedDates.length > 1) {
            // Single date mode: Ensure only one date is selected
            this.selectedDates = [this.selectedDates[0]];
            console.log('Ensured single date selection on hide');
        }
    }

    // Log the current state of selected dates and ranges
    console.log('Selected Dates:', JSON.stringify(this.selectedDates));
    console.log('Selected Ranges:', JSON.stringify(this.selectedRanges));
},

handleDateClick(date, isDisabled, isPreOccupied) {
    if (isDisabled || isPreOccupied) return;

    const isRangeInvalid = (range) => {
        // Check if any date in the range is disabled, pre-occupied, or overlaps an existing range
        if (range.length < 2) return false; // Skip incomplete ranges
        return (
            range.some(date => this.disableDates.includes(date) || this.preOccupiedDates.includes(date)) ||
            this.isRangeOverlapping(range)
        );
    };

    if (this.picker === 'single') {
        // Single picker logic remains unchanged
        if (this.multiSelect) {
            if (this.selectedDates.includes(date)) {
                this.selectedDates = this.selectedDates.filter(d => d !== date);
            } else {
                this.selectedDates.push(date);
            }
        } else {
            this.selectedDates = [date];
        }
    } else if (this.picker === 'range') {
        if (!this.multiSelect) {
            // Single range logic remains unchanged
            if (this.selectedRanges.length === 1) {
                const lastRange = this.selectedRanges[0];

                if (lastRange.length === 1 && lastRange[0] === date) {
                    this.selectedRanges = [[date, date]];
                } else {
                    const range = this.getDatesInRange(lastRange[0], date);

                    if (isRangeInvalid(range)) {
                        console.log('Invalid range due to disabled, preoccupied dates, or overlapping ranges');
                        this.selectedRanges = [];
                    } else {
                        this.selectedRanges = [range];
                    }
                }
            } else {
                this.selectedRanges = [[date]];
            }
        } else {
            // Multi-range logic with overlap check
            const lastRange = this.selectedRanges[this.selectedRanges.length - 1];

            if (lastRange && lastRange.length === 1) {
                if (lastRange[0] === date) {
                    this.selectedRanges[this.selectedRanges.length - 1] = [date, date];
                } else {
                    const range = this.getDatesInRange(lastRange[0], date);

                    if (isRangeInvalid(range)) {
                        console.log('Invalid range due to disabled, preoccupied dates, or overlapping ranges');
                        this.selectedRanges.pop();
                    } else {
                        this.selectedRanges[this.selectedRanges.length - 1] = range;
                    }
                }
            } else {
                this.selectedRanges.push([date]);
            }
        }
    }

    console.log('Selected Dates:', JSON.stringify(this.selectedDates));
    console.log('Selected Ranges:', JSON.stringify(this.selectedRanges));
},

isRangeOverlapping(newRange) {
    // Check only complete ranges (start and end dates present)
    const [newStart, newEnd] = [new Date(newRange[0]), new Date(newRange[newRange.length - 1])];
    if (!newStart || !newEnd) return false; // Skip incomplete ranges

    return this.selectedRanges.some(existingRange => {
        if (existingRange.length < 2) return false; // Skip incomplete existing ranges
        const [existingStart, existingEnd] = [
            new Date(existingRange[0]),
            new Date(existingRange[existingRange.length - 1]),
        ];

        // Check for overlap
        return newStart <= existingEnd && newEnd >= existingStart;
    });
},



        isSelected(date) {
            // Check if a date is selected in 'single' mode
            if (this.picker === 'single') {
                return this.selectedDates.includes(date);
            }
            // Check if a date belongs to any range in 'range' mode
            return this.selectedRanges.some(range => range.includes(date));
        },

        getDatesInRange(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const dates = [];

            while (start <= end) {
                dates.push(start.toISOString().split('T')[0]);
                start.setDate(start.getDate() + 1);
            }

            return dates;
        },

        prevMonth() {
            this.currentMonth -= 1;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear -= 1;
            }
            this.generateCalendar();
        },

        nextMonth() {
            this.currentMonth += 1;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear += 1;
            }
            this.generateCalendar();
        },
    };
}



function generateDateRange(dates) {
    const result = [];
    dates.forEach(range => {
        if (!range.startDate) return;
        const start = new Date(range.startDate);
        const end = range.endDate ? new Date(range.endDate) : start;
        while (start <= end) {
            result.push(start.toISOString().split('T')[0]);
            start.setDate(start.getDate() + 1);
        }
    });
    return result;
}



</script>

<style>
 body, html {
    overflow: visible;
}

.calendar-container {
    position: relative;
    display: inline-block;
    overflow: visible; /* Ensure the calendar is not clipped */
}

.calendar {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    width: 300px;
    padding: 10px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    z-index: 1000;
}

.calendar[x-show="true"] {
    display: block;
}

.days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.day {
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
    cursor: pointer;
}

.day.empty {
    background-color: #f9f9f9;
    cursor: default;
}

.day.selected {
    background-color: #007bff;
    color: white;
    font-weight: bold;
}

.day.pre-occupied {
    background-color: #2bff00;
    color: rgb(0, 0, 0);
    font-style: italic;
    cursor: not-allowed;
}

.day.disabled {
    text-decoration: line-through;
    color: #999;
    cursor: not-allowed;
}

.navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.header {
    font-weight: bold;
    text-align: center;
    background-color: #f3f3f3;
}

</style>
<script>
    function check303() {
        alert('check 303');    
    }
</script>

@endassets
