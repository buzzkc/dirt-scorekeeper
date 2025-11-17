// Basic JS for UI niceties (no frameworks, mobile-friendly)
document.addEventListener('submit', function(e){
    if (e.target.matches('form')) {
        // For forms that post to API, use fetch to avoid full reload if desired.
        // Currently let forms submit normally except the API form which is handled below.
    }
});

// Intercept API round saves to show emoji without full page reload
document.addEventListener('submit', async function(e){
    const form = e.target;
    if (form.closest('section') && form.action && form.action.endsWith('api/save_round.php')) {
        e.preventDefault();
        const data = new FormData(form);
        try {
            const res = await fetch(form.action, { method: 'POST', body: data });
            const json = await res.json();
            if (json.success) {
                // show a small toast and reload to show saved row
                alert('Round saved.');
                window.location.reload();
            } else {
                alert('Error saving round.');
            }
        } catch (err) {
            alert('Network error.');
        }
    }
});
