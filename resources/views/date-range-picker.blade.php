<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CodeFons Date Range Picker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center mb-4">Booking Form</h1>
                <form action="{{ route('booking.date') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name">
                    </div>

                    <div class="mb-3">
                        <label for="booking_range" class="form-label">Booking Range</label>
                        @livewire('date-range-picker', [
                            'inputAttributes' => [
                                'name' => 'booking_range',
                                'id' => 'booking_range',
                                'class' => 'form-control',
                                'style' => 'width: 100%;
                                            padding: 10px;
                                            border: 1px solid #ccc;
                                            border-radius: 5px;
                                            font-size: 14px;
                                            color: #333;
                                            outline: none;
                                            background-color: #f9f9f9;
                                            transition: border-color 0.3s;',
                            ],  
                            'picker' => 'range',
                            'multiSelect' => true,
                            'initialMonthYear' => '2024-10',
                            'defaultHoverText' => '',
                            'preOccupiedDates' => [
                                [
                                    'startDate' => '2024-12-25',
                                    'endDate' => '2024-12-25',
                                ],
                                [
                                    'startDate' => '2024-12-26',
                                    'endDate' => '2024-12-26',
                                ],
                                [
                                    'startDate' => '2024-12-1',
                                    'endDate' => '2024-12-4',
                                ]
                            ],
                            'disableDates' => [
                                [
                                    'startDate' => '2024-12-21',
                                    'endDate' => '2024-12-23',
                                ],
                                [
                                    'startDate' => '2024-12-9',
                                    'endDate' => '2024-12-13',
                                ]
                            ],
                        ])
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
