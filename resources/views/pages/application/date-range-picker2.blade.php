<x-app-layout>
    <x-slot name="title">
        Date Range Picker
    </x-slot>
    @livewire('date-range-picker2', [
        'type' => 'range',
        'multipleSelection' => true,
        'inputAttributes' => [
            'id' => 'my-date-range-picker',
            'name' => 'my-date-range-name',
            'style' => 'color:red;',
            'class' => 'my input bg-7',
            'autocomplete' => "off",
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