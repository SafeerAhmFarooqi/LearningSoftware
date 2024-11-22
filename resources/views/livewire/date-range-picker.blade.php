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

      
       <div class="calendar" x-show="open" x-transition x-cloak @click.outside="open = false">
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
        open: false, // Controls calendar visibility
        multiSelect, // Whether multiple dates can be selected
        picker, // 'single' or 'range' selection
        preOccupiedDates: generateDateRange(preOccupiedDates), // Pre-occupied dates
        disableDates: generateDateRange(disableDates), // Disabled dates
        currentMonth: new Date().getMonth(), // Current displayed month (0-11)
        currentYear: new Date().getFullYear(), // Current displayed year
        selectedDates: [], // Array of selected dates
        calendarDays: [], // Array to hold the days of the month
        months: [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ],
        weekdays: ["S", "M", "T", "W", "T", "F", "S"], // Weekday headers

        // Generate the calendar days for the current month and year
        generateCalendar() {
            const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();

            const days = [];
            // Add leading empty days
            for (let i = 0; i < firstDay; i++) {
                days.push({ label: "", empty: true });
            }
            // Add actual days of the month
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

        // Handle a date click
        handleDateClick(date, isDisabled, isPreOccupied) {
            if (isDisabled || isPreOccupied) return;

            if (this.picker === 'single') {
                this.selectedDates = [date];
            } else if (this.picker === 'range') {
                if (this.selectedDates.length === 2) {
                    this.selectedDates = [date];
                } else {
                    this.selectedDates.push(date);
                    this.selectedDates.sort();
                }
            } else if (this.multiSelect) {
                if (this.selectedDates.includes(date)) {
                    this.selectedDates = this.selectedDates.filter(d => d !== date);
                } else {
                    this.selectedDates.push(date);
                }
            }
        },

        // Navigate to the previous month
        prevMonth() {
            this.currentMonth -= 1;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear -= 1;
            }
            this.generateCalendar();
        },

        // Navigate to the next month
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

// Utility function to generate a range of dates
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
    background-color: #ccc;
    color: white;
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
