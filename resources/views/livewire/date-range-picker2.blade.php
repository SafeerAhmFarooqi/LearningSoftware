<div>
    <div class="calendar-container">
        <input wire:model="selectedDateString" type="text"
        @foreach ($inputAttributes as $keyAttribute => $inputAttribute)
        {{ ' ' }}{{ $keyAttribute }}{{'='}} '{{ $inputAttribute }}'
        @endforeach
        >
        <div class="calendar">
            <div class="navigation">
                <button type="button">&lt; Prev</button>
                <div class="month">
                    <select id="monthSelect">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="year">
                    <input type="number" id="yearInput" placeholder="Year" min="1900" max="2100">
                </div>
                <button type="button">Next &gt;</button>
            </div>
            <div class="days">
                <div class="day">S</div>
                <div class="day">M</div>
                <div class="day">T</div>
                <div class="day">W</div>
                <div class="day">T</div>
                <div class="day">F</div>
                <div class="day">S</div>
                <div class="day"></div>
                <div class="day"></div>
                <div class="day"></div>
                <div class="day">1</div>
                <div class="day">2</div>
                <div class="day">3</div>
                <div class="day">4</div>
                <div class="day">5</div>
                <div class="day">6</div>
                <div class="day">7</div>
                <div class="day">8</div>
                <div class="day">9</div>
                <div class="day">10</div>
                <div class="day">11</div>
                <div class="day">12</div>
                <div class="day">13</div>
                <div class="day">14</div>
                <div class="day">15</div>
                <div class="day">16</div>
                <div class="day">17</div>
                <div class="day">18</div>
                <div class="day">19</div>
                <div class="day">20</div>
                <div class="day">21</div>
                <div class="day">22</div>
                <div class="day">23</div>
                <div class="day">24</div>
                <div class="day">25</div>
                <div class="day">26</div>
                <div class="day">27</div>
                <div class="day">28</div>
                <div class="day">29</div>
                <div class="day">30</div>
            </div>
            <div class="apply-button">
                <button id="applyButton" type="button">Apply</button>
            </div>
        </div>
    </div>
</div>

@assets
<style>
       body {
        font-family: Arial, sans-serif;
    }
    .calendar-container {
        position: relative;
        display: inline-block;
    }
    .calendar {
        width: 300px;
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        background-color: #fff;
        display: none;
    }
    .month {
        text-align: center;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }
    .day {
        position: relative;
        padding: 5px;
        border: 1px solid #ccc;
        text-align: center;
        cursor: pointer;
    }
    .day:hover::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(109, 109, 204, 0.5) 0%, rgba(114, 114, 199, 0.562) 100%);
       
    }
    .navigation {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .apply-button{
        margin-top: 10px;
    }
</style>
@endassets

@script
<script>
      const calendarInput = document.getElementById("{{$inputAttributes['id']}}");
        const calendarContainer = document.querySelector('.calendar-container');
        const calendar = document.querySelector('.calendar');

        calendarInput.addEventListener('click', function(event) {
            event.stopPropagation();
            calendar.style.display = 'block';
        });

        document.addEventListener('click', function(event) {
            const isCalendarClicked = calendar.contains(event.target);
            const isInputClicked = calendarInput.contains(event.target);
            
            if (!isCalendarClicked && !isInputClicked) {
                calendar.style.display = 'none';
            }
        });
</script>
@endscript
