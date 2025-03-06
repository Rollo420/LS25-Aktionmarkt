@props(['type', 'data', 'options'])

<head>
    @vite(['resources/sass/app.scss'])


</head>
<div class="chartContainer">
    <canvas class="chartCanvas"></canvas>
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