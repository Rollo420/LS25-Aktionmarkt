import Echo from 'laravel-echo';

document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Echo !== 'undefined') {
        window.Echo.channel('timeskip')
            .listen('.timeskip.completed', (e) => {
                console.log('Timeskip completed:', e.message);

                // Refresh the page to show updated data
                window.location.reload();
            });
    }
});
