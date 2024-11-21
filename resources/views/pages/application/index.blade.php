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
        console.log('check 1');
        console.log('Response status code:', response.status); // Log response status code
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        //console.log(response.json(),'safeer 1');
       
        return response.json();
    })
    .then(data => {
        console.log('check 2');
        console.log('Fetch API Asynchronous Request Successful:', data);
    })
    .catch(error => {
        console.log('check 3');
        console.error('Fetch API Asynchronous Request Failed:', error.message);
    });
    </script>
@endsection
</x-app-layout>