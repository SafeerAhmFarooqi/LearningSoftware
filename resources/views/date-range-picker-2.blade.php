<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Date Range Picker</title>
</head>
<body>
    <form action="{{route('booking.date')}}" method="post">
        <label for="name">Your Name</label><br>
        <input type="text" id="name" name="name"><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>