// assets/js/main.js
$(document).ready(function () {
    // Side Navigation Toggle
    const sideNav = $('#sideNav');
    const overlay = $('#overlay');

    function openNav() {
        sideNav.addClass('open');
        overlay.addClass('show');
        // Prevent scrolling on body when nav is open
        $('body').css('overflow', 'hidden');
    }

    function closeNav() {
        sideNav.removeClass('open');
        overlay.removeClass('show');
        $('body').css('overflow', '');
    }

    $('#navToggle').on('click', openNav);
    $('#closeNav').on('click', closeNav);
    overlay.on('click', closeNav);

    // Add to Cart AJAX
    $('.add-to-cart').on('click', function (e) {
        e.preventDefault();

        let itemId = $(this).data('id');
        let btn = $(this);
        let originalText = btn.html();

        // Visual feedback
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Adding...');
        btn.prop('disabled', true);

        $.ajax({
            url: 'ajax/add_to_cart.php',
            type: 'POST',
            data: {
                item_id: itemId,
                quantity: 1
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Update cart count badge
                    $('#cartCount').text(response.cartCount);

                    // Show success
                    btn.html('<i class="fa-solid fa-check"></i> Added');
                    btn.removeClass('btn-primary').addClass('btn-success');

                    setTimeout(function () {
                        btn.html(originalText);
                        btn.removeClass('btn-success').addClass('btn-primary');
                        btn.prop('disabled', false);
                    }, 2000);
                } else {
                    alert(response.message || 'Error adding to cart. Please log in.');
                    btn.html(originalText);
                    btn.prop('disabled', false);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                }
            },
            error: function () {
                alert('Something went wrong. Please try again.');
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });

    // Toggle Favorite AJAX
    $(document).on('click', '.toggle-favorite', function (e) {
        e.preventDefault();
        let btn = $(this);
        let itemId = btn.data('id');
        let icon = btn.find('i');

        $.ajax({
            url: 'ajax/toggle_favorite.php',
            type: 'POST',
            data: { item_id: itemId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.action === 'added') {
                        icon.removeClass('fa-regular').addClass('fa-solid');
                    } else {
                        icon.removeClass('fa-solid').addClass('fa-regular');
                    }
                } else {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message || 'Error toggling favorite.');
                    }
                }
            }
        });
    });

    // Live Search
    $('#searchInput').on('input', function () {
        let query = $(this).val();

        $.ajax({
            url: 'ajax/search_menu.php',
            type: 'GET',
            data: { q: query },
            success: function (response) {
                $('#menuGrid').html(response);
            }
        });
    });

    // Dark Mode Toggle
    const themeToggle = $('#themeToggle');
    const body = $('body');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme === 'dark') {
        body.addClass('dark-mode');
        themeToggle.html('<i class="fa-solid fa-sun"></i>');
    }

    themeToggle.on('click', function () {
        body.toggleClass('dark-mode');
        let theme = 'light';
        if (body.hasClass('dark-mode')) {
            theme = 'dark';
            themeToggle.html('<i class="fa-solid fa-sun"></i>');
        } else {
            themeToggle.html('<i class="fa-solid fa-moon"></i>');
        }
        localStorage.setItem('theme', theme);
    });

    // Scroll Reveal Animation
    function reveal() {
        var reveals = $('.reveal');
        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 100;
            if (elementTop < windowHeight - elementVisible) {
                $(reveals[i]).addClass('active');
            }
        }
    }
    window.addEventListener('scroll', reveal);
    reveal(); // Trigger on load
});
