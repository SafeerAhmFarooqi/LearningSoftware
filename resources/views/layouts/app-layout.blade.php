<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('includes.styles.master-styles')
    @yield('styles')
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{isset($title)?($title!=''?$title:config('app.name')):config('app.name')}}</title>
</head>
<body>
    {{$slot}}
    @include('includes.scripts.master-scripts')
    @yield('scripts')
</body>
</html>