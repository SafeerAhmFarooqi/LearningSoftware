<x-app-layout>
    <x-slot name="title">
        Date Range Picker
    </x-slot>
    @livewire('date-range-picker', [
        'selector' => 'range', //type : string and  options : single or range
        'multipleSelection' => true, //type : Boolean and  options : true or false
        'inputAttributes' => [
            'id' => '', //type : string
            'name' => 'my-date-range-name', //type : string
            'style' => 'color:red;', //type : string
            'class' => 'my input bg-7', //type : string
            'autocomplete' => "off", //type : string
            'placeholder' => 'safeer', //type : string
            // 'selectedDates' => [
            //     [
            //         'startDate' => '15/12/2024', //format d/m/Y
            //         'endDate' => '17/12/2024',//format d/m/Y
            //     ],[
            //         'startDate' => '8/12/2024',//format d/m/Y
            //         'endDate' => '9/12/2024',//format d/m/Y
            //     ],[
            //         'startDate' => '18/12/2024',//format d/m/Y
            //         'endDate' => '22/12/2024',//format d/m/Y
            //     ]
            // ],
        ],
        ])
@section('styles')
<style>
 
</style>
@endsection
@section('scripts')
    <script>

    </script>
@endsection
</x-app-layout>