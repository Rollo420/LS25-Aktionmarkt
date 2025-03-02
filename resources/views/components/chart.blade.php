@props(['type', 'data', 'options'])

<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<div class="chartContainer">
    <canvas class="chartCanvas"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.querySelector('.chartCanvas').getContext('2d');
            new Chart(ctx, {
                type: '{{ $type }}',
                data: {!! json_encode($data) !!},
                options: {!! json_encode($options) !!}
            });
        });
    </script>
</div>
