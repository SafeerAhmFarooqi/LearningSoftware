<div>
    <div class="calendar-container" 
         x-data="calendarApp({{ json_encode($multiSelect) }}, '{{ $picker }}', {{ json_encode($preOccupiedDates) }}, {{ json_encode($disableDates) }})" 
         x-init="generateCalendar()">
       <!-- Dynamic Input Field -->
       <input 
           wire:model="selectedDates"
           type="text"
           x-bind:value="selectedDates.join(', ')"
           @foreach ($inputAttributes as $keyAttribute => $inputAttribute)
               @if (!is_string($inputAttribute)) @continue @endif
               {{ ' ' }}{{ $keyAttribute }}="{{ e($inputAttribute) }}"
           @endforeach
           @click="open = !open; console.log('Open state:', open)"
           readonly
           aria-label="Selected Dates"
       >

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
                           'selected': selectedDates.includes(day.date),
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
        selectedDates: [],
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

        handleDateClick(date, isDisabled, isPreOccupied) {
    if (isDisabled || isPreOccupied) return;

    if (this.picker === 'single' && this.multiSelect) {
        // Multi-select individual dates
        if (this.selectedDates.includes(date)) {
            // Remove the date if already selected
            this.selectedDates = this.selectedDates.filter(d => d !== date);
        } else {
            // Add the date
            this.selectedDates.push(date);
        }
    } else if (this.picker === 'single') {
        // Single date selection
        this.selectedDates = [date];
    } else if (this.picker === 'range' && this.multiSelect) {
        // Multi-select ranges
        const lastRange = this.selectedDates[this.selectedDates.length - 1];
        if (lastRange && lastRange.length === 1) {
            // Complete the current range
            const range = [lastRange[0], date].sort(); // Sort to ensure start <= end
            this.selectedDates[this.selectedDates.length - 1] = range;
        } else {
            // Start a new range
            this.selectedDates.push([date]);
        }
    } else if (this.picker === 'range') {
        // Single range selection (multiSelect: false)
        if (this.selectedDates.length === 2) {
            // Reset if any range exists or a click falls inside an existing range
            this.selectedDates = [date];
        } else if (this.selectedDates.length === 1) {
            // Complete the range
            this.selectedDates.push(date);
            this.selectedDates.sort(); // Ensure start <= end
            // Highlight all dates in the range
            const rangeDates = this.getDatesInRange(this.selectedDates[0], this.selectedDates[1]);
            this.selectedDates = rangeDates; // Replace with full range
        } else {
            // Start a new range
            this.selectedDates = [date];
        }
    }

    console.log('Selected Dates Array:', JSON.stringify(this.selectedDates));
},
handleCalendarHide() {
    this.open = false;

    if (this.picker === 'range' && !this.multiSelect) {
        if (this.selectedDates.length < 2) {
            // Clear incomplete range
            this.selectedDates = [];
            console.log('Cleared incomplete range on hide');
        } else {
            console.log('Valid range applied on hide:', this.selectedDates);
        }
    }
},


/**
 * Utility function to generate all dates in a range
 * @param {String} startDate - The start date (YYYY-MM-DD)
 * @param {String} endDate - The end date (YYYY-MM-DD)
 * @returns {Array} Array of dates in the range
 */
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
