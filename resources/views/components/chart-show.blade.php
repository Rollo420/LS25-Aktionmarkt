@props(['type', 'data', 'options'])

<div class="chartContainer w-full h-64 sm:h-80 md:h-96 lg:h-[400px] xl:h-[450px] 2xl:h-[500px] 3xl:h-[550px] 4xl:h-[600px] 5xl:h-[650px] 6xl:h-[700px] 7xl:h-[750px]">
    <canvas class="chartCanvas w-full h-full"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.querySelector('.chartCanvas').getContext('2d');
            new Chart(ctx, {
                type: '{{ $type }}',
                data: @json($data),
                options: @json($options)
            });
        });
    </script>
</div>
