<x-app-layout>
    <x-slot name="title">
    Shooting Game
    </x-slot>


    <canvas id="gameCanvas"></canvas>


@section('styles')
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }

        canvas {
            display: block;
        }
    </style>
@endsection
@section('scripts')
    <script>
         const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const targets = [];

        class Target {
            constructor(x, y, radius, color) {
                this.x = x;
                this.y = y;
                this.radius = radius;
                this.color = color;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
                ctx.closePath();
            }
        }

        function createTarget() {
            const x = Math.random() * canvas.width;
            const y = Math.random() * canvas.height;
            const radius = 30;
            const color = 'red';
            const target = new Target(x, y, radius, color);
            targets.push(target);
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (const target of targets) {
                target.draw();
            }

            requestAnimationFrame(animate);
        }

        canvas.addEventListener('click', (event) => {
            const mouseX = event.clientX;
            const mouseY = event.clientY;

            for (let i = targets.length - 1; i >= 0; i--) {
                const target = targets[i];
                const distance = Math.sqrt((mouseX - target.x) ** 2 + (mouseY - target.y) ** 2);

                if (distance < target.radius) {
                    targets.splice(i, 1);
                    createTarget();
                }
            }
        });

        setInterval(createTarget, 2000); // Create a new target every 2 seconds
        animate();
    </script>
@endsection
</x-app-layout>