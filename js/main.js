function confirmDelete(msg) {
    return confirm(msg || 'Are you sure you want to delete this? This action cannot be undone.');
}

document.addEventListener('DOMContentLoaded', function () {
    // Animate XP and performance bars
    document.querySelectorAll('.xp-fill, .perf-fill').forEach(function (bar) {
        var w = bar.getAttribute('data-width') || '0';
        bar.style.width = '0%';
        setTimeout(function () { bar.style.width = w + '%'; }, 250);
    });
// Auto-dismiss alerts after 4 seconds
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        }, 4000);
    });
});
