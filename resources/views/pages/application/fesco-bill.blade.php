<x-app-layout>
    <x-slot name="title">
    Javascript
    </x-slot>
<h1>ahmed farooqi</h1>
@section('scripts')
    <script>
    //fetch('https://jsonplaceholder.typicode.com/posts/1')
    fetch('{{route('json.response')}}')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Fetch API Asynchronous Request Successful:', data);
    })
    .catch(error => {
        console.error('Fetch API Asynchronous Request Failed:', error.message);
    });
    </script>
@endsection
</x-app-layout>