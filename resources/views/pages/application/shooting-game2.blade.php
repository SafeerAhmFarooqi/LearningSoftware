<x-app-layout>
    <x-slot name="title">
    Shooting Game
    </x-slot>


    <div id="box"></div>


@section('styles')
    <style>
      body {
      margin: 0;
      overflow: hidden;
    }

    #box {
      width: 50px;
      height: 50px;
      background-color: blue;
      position: absolute;
    }
    </style>
@endsection
@section('scripts')
    <script>
     const box = document.getElementById('box');

document.addEventListener('mousemove', (event) => {
  const mouseX = event.clientX;
  const mouseY = event.clientY;

  // Adjust box position based on mouse coordinates
  box.style.left = mouseX - box.offsetWidth / 2 + 'px';
  box.style.top = mouseY - box.offsetHeight / 2 + 'px';
});
    </script>
@endsection
</x-app-layout>