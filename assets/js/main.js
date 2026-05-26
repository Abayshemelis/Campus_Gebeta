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

    // --- Rating System JS ---
    const ratingModal = $('#ratingModal');
    const modalOverlay = $('#modalOverlay');

    function openRatingModal(itemId, itemName) {
        $('#ratingFormItemId').val(itemId);
        $('#modalItemName').text(itemName + ' Reviews');
        $('#reviewsList').html('<p style="text-align:center;color:var(--gray);"><i class="fa-solid fa-spinner fa-spin"></i> Loading reviews...</p>');
        $('#modalAvgRating').text('0.0');
        $('#modalAvgStars').html('');
        $('#modalReviewsCount').text('Based on 0 reviews');
        
        // Reset form
        if ($('#ratingForm').length) {
            $('#ratingForm')[0].reset();
            $('#submitReviewTitle').text('Rate this product');
        }

        // Fetch existing reviews
        $.ajax({
            url: 'ajax/get_reviews.php',
            type: 'GET',
            data: { menu_item_id: itemId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#modalAvgRating').text(response.avg_rating);
                    
                    // Render average stars
                    let starsHtml = '';
                    let avg = parseFloat(response.avg_rating);
                    for (let i = 1; i <= 5; i++) {
                        if (i <= Math.round(avg)) {
                            starsHtml += '<i class="fa-solid fa-star"></i> ';
                        } else {
                            starsHtml += '<i class="fa-regular fa-star"></i> ';
                        }
                    }
                    $('#modalAvgStars').html(starsHtml);
                    $('#modalReviewsCount').text('Based on ' + response.reviews_count + ' review(s)');

                    // Render reviews list
                    if (response.reviews.length > 0) {
                        let listHtml = '';
                        response.reviews.forEach(function (r) {
                            let revStars = '';
                            for (let i = 1; i <= 5; i++) {
                                if (i <= r.rating) {
                                    revStars += '<i class="fa-solid fa-star"></i>';
                                } else {
                                    revStars += '<i class="fa-regular fa-star"></i>';
                                }
                            }
                            listHtml += `
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="review-author">${r.user_name}</span>
                                        <span class="review-date">${r.created_at}</span>
                                    </div>
                                    <div class="review-stars" style="margin-bottom:8px;">${revStars}</div>
                                    <p class="review-comment">${r.comment || '<i>No comment left.</i>'}</p>
                                </div>
                            `;
                        });
                        $('#reviewsList').html(listHtml);
                    } else {
                        $('#reviewsList').html('<p style="text-align:center;color:var(--gray);padding:15px 0;">No reviews yet. Be the first to review!</p>');
                    }

                    // Populate my review if exists
                    if (response.my_review && $('#ratingForm').length) {
                        $('#submitReviewTitle').text('Update your review');
                        $(`input[name="rating"][value="${response.my_review.rating}"]`).prop('checked', true);
                        $('#ratingComment').val(response.my_review.comment);
                    }
                } else {
                    $('#reviewsList').html('<p style="text-align:center;color:var(--danger);">' + (response.message || 'Error loading reviews') + '</p>');
                }
            },
            error: function () {
                $('#reviewsList').html('<p style="text-align:center;color:var(--danger);">Error connecting to server.</p>');
            }
        });

        ratingModal.addClass('show');
        modalOverlay.addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function closeRatingModal() {
        ratingModal.removeClass('show');
        modalOverlay.removeClass('show');
        $('body').css('overflow', '');
    }

    // Bind click events on card rating badges
    $(document).on('click', '.card-rating-badge', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        openRatingModal(itemId, itemName);
    });

    $('#closeRatingModal').on('click', closeRatingModal);
    modalOverlay.on('click', closeRatingModal);

    // Form Submission
    $('#ratingForm').on('submit', function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        let btn = $(this).find('button[type="submit"]');
        let originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url: 'ajax/submit_rating.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                btn.prop('disabled', false).html(originalHtml);
                if (response.success) {
                    alert(response.message || 'Review submitted successfully!');
                    let itemId = $('#ratingFormItemId').val();
                    let itemName = $('#modalItemName').text().replace(' Reviews', '');
                    
                    // Update badges in UI if they exist
                    $(`.rating-val-${itemId}`).text(response.avg_rating);
                    $(`.rating-count-${itemId}`).text('(' + response.reviews_count + ')');

                    // Refresh modal reviews list
                    openRatingModal(itemId, itemName);
                } else {
                    alert(response.message || 'Error submitting rating.');
                }
            },
            error: function () {
                btn.prop('disabled', false).html(originalHtml);
                alert('Error connecting to the server. Please try again.');
            }
        });
    });
});
