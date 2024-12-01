<div>
    <div class="calendar-container" 
        x-data="calendarApp(
            {{ json_encode($multiSelect) }}, 
            '{{ $picker }}', 
            {{ json_encode($preOccupiedDates) }}, 
            {{ json_encode($disableDates) }}, 
            '{{ $initialMonthYear }}',
            '{{ $defaultHoverText }}' 
        )" 
        x-init="generateCalendar()">
       <!-- Dynamic Input Field -->
       <input 
        wire:model="selectedDates"
        type="text"
        x-bind:value="picker === 'range' 
                        ? selectedRanges.map(range => `[${range.join(', ')}]`).join(', ') 
                        : selectedDates.join(', ')"
        x-bind:name="{{ isset($inputAttributes['name']) 
                        ? "'" . e($inputAttributes['name']) . "'" 
                        : "(picker === 'range' ? 'selectedRanges' : 'selectedDates')" }}"
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
                <select x-model="currentMonth" @change="generateCalendar()" x-init="$nextTick(() => $el.value = currentMonth)">
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
                     @mouseenter="hoverText = getHoverText(day.date)"
                     @mouseleave="hoverText = ''"
                     @click="handleDateClick(day.date, day.disabled, day.preOccupied)">
                    <span x-text="day.label"></span>
                    <div class="hover-info" x-show="hoverText && !day.empty" x-text="hoverText"></div>
                </div>
            </template>
            
           </div>
           <div class="clear-container">
            <button 
                type="button" 
                @click="clearSelection()" 
                class="clear-button">
                Clear Selection
            </button>
        </div>
       </div>
    </div>
</div>




@assets
<script>
   function calendarApp(multiSelect = false, picker = 'single', preOccupiedDates = [], disableDates = [], initialMonthYear, defaultHoverText = '') {
    return {
        open: false,
        multiSelect: multiSelect === true || multiSelect === 'true', // Ensure boolean
        picker,
        defaultHoverText : defaultHoverText,
        preOccupiedDates: generateDateRange(preOccupiedDates),
        disableDates: generateDateRange(disableDates),
        currentMonth: initialMonthYear && initialMonthYear.includes('-') 
            ? parseInt(initialMonthYear.split('-')[1]) - 1 
            : new Date().getMonth(),
        currentYear: initialMonthYear && initialMonthYear.includes('-') 
            ? parseInt(initialMonthYear.split('-')[0]) 
            : new Date().getFullYear(),
        selectedDates: [], // To store dates for 'single' mode
        selectedRanges: [], // To store ranges for 'range' mode
        calendarDays: [],
        livewireHoverData : '',
        hoverText : '',
        hoverData: {},
        months: [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ],
        weekdays: ["S", "M", "T", "W", "T", "F", "S"],
        clearSelection() {
            if (this.picker === 'single') {
                this.selectedDates = [];
            } else if (this.picker === 'range') {
                this.selectedRanges = [];
            }
            
            // Reset hover text
            this.hoverText = '';

            // Call Livewire method to clear backend state
            @this.call('clearSelection');

            console.log('Selections cleared');
            this.loadHoverData();
        },

        generateCalendar() {
            const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();

            const days = [];
            for (let i = 0; i < firstDay; i++) {
                days.push({ label: "", empty: true });
            }
            for (let i = 1; i <= daysInMonth; i++) {
                const date = `${this.currentYear}-${parseInt(this.currentMonth) + 1}-${String(i).padStart(2, '0')}`;
                days.push({
                    label: i,
                    date,
                    empty: false,
                    preOccupied: this.preOccupiedDates.includes(date),
                    disabled: this.disableDates.includes(date),
                });
            }
            this.calendarDays = days;
            this.loadHoverData();
        },
        loadHoverData() {
    this.hoverData = {};
    const monthYear = `${this.currentYear}-${parseInt(this.currentMonth) + 1}`;

    // Call the Livewire method
    @this.call('getLivewireHoverData', monthYear)
        .then(livewireHoverData => {
            if (livewireHoverData && typeof livewireHoverData === 'object') {
                this.calendarDays.forEach(day => {
                    if (!day.empty) {
                        const formattedDate = `${this.currentYear}-${parseInt(this.currentMonth) + 1}-${String(day.label).padStart(2, '0')}`;
                     
                        // Safely access livewireHoverData
                        this.hoverData[formattedDate] = 
                        (livewireHoverData[day.label] !== undefined && livewireHoverData[day.label] !== null && livewireHoverData[day.label] !== '') 
                            ? livewireHoverData[day.label] 
                            : (defaultHoverText ? defaultHoverText : `${formattedDate}`);
                    }
                });
            } else {
                console.error('Invalid hover data received:', livewireHoverData);
            }
        })
        .catch(error => {
            console.error('Error fetching hover data:', error);
        });
},
        getHoverText(date) {
            return this.hoverData[date] || "";
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
        if (range.length < 2) return false; // Skip incomplete ranges
        return (
            range.some(date => this.disableDates.includes(date) || this.preOccupiedDates.includes(date)) ||
            this.isRangeOverlapping(range)
        );
    };

    if (this.picker === 'single') {
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

    // Cleanup step to remove empty subarrays and return [] if all are empty
    this.selectedRanges = this.selectedRanges.filter(range => range.length > 0);
    if (this.selectedRanges.length === 0) {
        this.selectedRanges = [];
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
        if (!range.startDate) return; // Skip if no startDate

        // Parse the dates and ensure they are treated as local dates
        let start = new Date(`${range.startDate}T00:00:00`);
        const end = range.endDate
            ? new Date(`${range.endDate}T00:00:00`)
            : new Date(`${range.startDate}T00:00:00`);

        // Iterate through the range and include all dates
        while (start <= end) {
            result.push(
                `${start.getFullYear()}-${String(start.getMonth() + 1).padStart(2, '0')}-${String(start.getDate()).padStart(2, '0')}`
            );
            start.setDate(start.getDate() + 1); // Increment date correctly
        }
    });
    return result;
}




</script>

<style>
  body, html {
    overflow: visible;
    font-family: 'Arial', sans-serif;
}

.calendar-container {
    position: relative;
    display: inline-block;
    overflow: visible;
}

.calendar {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    width: 320px;
    padding: 20px;
    background-color: #ffffff;
    border: 1px solid #e3e3e3;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: opacity 0.3s ease-in-out;
}

.calendar[x-show="true"] {
    display: block;
}

.navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.navigation button {
    background-color: #007bff;
    color: #ffffff;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.navigation button:hover {
    background-color: #0056b3;
}

.navigation select, .navigation input[type="number"] {
    border: 1px solid #ccc;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    outline: none;
    width: auto;
}

.navigation select {
    cursor: pointer;
}

.days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.day {
    padding: 10px;
    text-align: center;
    border: 1px solid transparent;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    font-size: 14px;
}

.day.empty {
    background-color: transparent;
    cursor: default;
}

.day.selected {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    transform: scale(1.1);
}

.day.pre-occupied {
    background-color: #ffcc00;
    color: #333;
    font-style: italic;
    cursor: not-allowed;
}

.day.disabled {
    text-decoration: line-through;
    color: #999;
    cursor: not-allowed;
}

.day:hover:not(.empty):not(.disabled):not(.pre-occupied) {
    background-color: #f0f8ff;
    color: #007bff;
    transform: scale(1.05);
}

.header {
    font-weight: bold;
    text-align: center;
    color: #555;
    padding: 10px 0;
    border-bottom: 1px solid #e3e3e3;
}

.hover-info {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: #fff;
    padding: 5px;
    border-radius: 5px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 10;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease-in-out;
}

.day:hover .hover-info {
    opacity: 1;
}

.clear-container {
    margin-top: 20px;
    text-align: center;
}

.clear-button {
    background-color: #ff4d4d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s, transform 0.2s;
}

.clear-button:hover {
    background-color: #cc0000;
    transform: scale(1.05);
}

</style>
<script>
    function check303() {
        alert('check 303');    
    }
</script>

@endassets
