<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CodeFons Date Range Picker</title>
</head>
<body>

    <form action="{{ route('booking.date') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name">
        </div>

        <div class="form-group">
            <label for="booking_range">Booking Range</label>
            @livewire('date-range-picker', [
                'inputAttributes' => [
                    'name' => 'booking_range',
                    'id' => 'booking_range',
                    'class' => 'form-control',
                ],  
                'picker' => 'range',  //single,range defaults to single
                'multiSelect' => true, //true,false defaults to false
                'initialMonthYear' => '2024-10',
                'preOccupiedDates' => [//selected dates from backened
                    [
                        'startDate' => '2024-12-25', //Y-m-d
                        'endDate' => '2024-12-25', //Y-m-d
                    ],
                    [
                        'startDate' => '2024-12-26', //Y-m-d
                        'endDate' => '2024-12-26', //Y-m-d
                    ],
                    [
                        'startDate' => '2024-12-1', //Y-m-d
                        'endDate' => '2024-12-4', //Y-m-d
                    ]
                ],
                'disableDates' => [//selected dates from backened
                    [
                        'startDate' => '2024-12-21', //Y-m-d
                        'endDate' => '2024-12-23', //Y-m-d
                    ],
                    [
                        'startDate' => '2024-12-9', //Y-m-d
                        'endDate' => '2024-12-13', //Y-m-d
                    ]
                ],
            ])
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    
</body>
</html>