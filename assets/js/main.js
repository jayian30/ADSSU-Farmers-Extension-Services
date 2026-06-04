// assets/js/main.js
$(document).ready(function() {
    // Sidebar Toggle
    $('#btn-toggle').on('click', function() {
        $('#sidebar').toggleClass('collapsed');
    });

    // Handle mobile sidebar toggle
    if ($(window).width() <= 768) {
        $('#sidebar').addClass('collapsed');
        $('#btn-toggle').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('#sidebar').removeClass('collapsed');
        });
    }

    $(window).resize(function() {
        if ($(window).width() <= 768) {
            $('#sidebar').addClass('collapsed');
        } else {
            $('#sidebar').removeClass('active');
            $('#sidebar').removeClass('collapsed');
        }
    });
});
