</main> <!-- Close main-content -->

<footer class="footer" style="background-color: var(--dark-color); color: var(--light-color); padding: 50px 0 20px; margin-top: 50px;">
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px; margin-bottom: 30px;">
        <!-- Brand Section -->
        <div class="footer-brand" style="flex: 1; min-width: 250px;">
            <a href="index.php" style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; text-decoration: none;">
                <img src="assets/images/gebeta_logo.svg" alt="Campus Gebeta Logo" style="height: 45px; width: 45px; object-fit: contain;">
                <span style="color: var(--white); font-size: 1.5rem; font-weight: 700;">Campus <span style="color: var(--primary-color);">Gebeta</span></span>
            </a>
            <p style="color: #aaa; line-height: 1.6; margin-bottom: 20px;">
                Digitizing campus dining for faster, smarter, and better food service. The ultimate food-ordering platform for university students in Ethiopia.
            </p>
            <div style="display: flex; gap: 15px;">
                <a href="https://t.me/campusgebeta" target="_blank" style="color: var(--white); background: rgba(255,255,255,0.1); width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; transition: var(--transition);"><i class="fa-brands fa-telegram"></i></a>
                <a href="https://instagram.com/campusgebeta" target="_blank" style="color: var(--white); background: rgba(255,255,255,0.1); width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; transition: var(--transition);"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" target="_blank" style="color: var(--white); background: rgba(255,255,255,0.1); width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; transition: var(--transition);"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-links" style="flex: 1; min-width: 200px;">
            <h4 style="color: var(--white); margin-bottom: 20px; font-size: 1.1rem; position: relative; padding-bottom: 10px;">Quick Links
                <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: var(--primary-color); border-radius: 2px;"></span>
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                <li><a href="index.php" style="color: #aaa; text-decoration: none; transition: var(--transition);"><i class="fa-solid fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i> Menu</a></li>
                <li><a href="about.php" style="color: #aaa; text-decoration: none; transition: var(--transition);"><i class="fa-solid fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i> About Us</a></li>
            </ul>
        </div>

        <!-- Contact Section -->
        <div class="footer-contact" style="flex: 1; min-width: 250px;">
            <h4 style="color: var(--white); margin-bottom: 20px; font-size: 1.1rem; position: relative; padding-bottom: 10px;">Contact Us
                <span style="position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background: var(--primary-color); border-radius: 2px;"></span>
            </h4>
            <p style="color: #aaa; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <i class="fa-solid fa-envelope" style="color: var(--primary-color);"></i>
                <a href="mailto:amazia1075@gmail.com" style="color: #aaa; text-decoration: none;">amazia1075@gmail.com</a>
            </p>
            <p style="color: #aaa; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <i class="fa-solid fa-phone" style="color: var(--primary-color);"></i>
                <span>+251 909 861 075</span>
            </p>
            <p style="color: #aaa; display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <i class="fa-solid fa-location-dot" style="color: var(--primary-color);"></i>
                <span>Hawassa University, Ethiopia</span>
            </p>
        </div>
    </div>

    <div class="footer-bottom" style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); color: #888;">
        <p style="margin: 0;">&copy; <?= date('Y') ?> Campus Gebeta. All rights reserved.</p>
    </div>
</footer>

<!-- Rating/Reviews Modal -->
<div class="overlay" id="modalOverlay" style="z-index: 1150;"></div>
<div class="rating-modal" id="ratingModal">
    <div class="rating-modal-header">
        <h3 id="modalItemName" style="margin: 0; color: var(--dark-color);">Food Item Reviews</h3>
        <button id="closeRatingModal" style="background: none; border: none; font-size: 24px; color: var(--dark-color); cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="rating-modal-body">
        <!-- Display Avg Rating -->
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
            <div style="font-size: 3rem; font-weight: 700; color: var(--dark-color);" id="modalAvgRating">0.0</div>
            <div>
                <div style="color: #f1c40f; font-size: 1.2rem;" id="modalAvgStars">
                    <!-- Stars JS generated -->
                </div>
                <div style="color: var(--gray); font-size: 0.9rem;" id="modalReviewsCount">Based on 0 reviews</div>
            </div>
        </div>

        <!-- Existing Reviews List -->
        <div id="reviewsListContainer" style="margin-bottom: 30px;">
            <h4 style="color: var(--dark-color);">Student Reviews</h4>
            <div id="reviewsList" style="margin-top: 15px;">
                <!-- Review items injected here -->
            </div>
        </div>

        <!-- Submit Review Form (if logged in) -->
        <?php if (isLoggedIn()): ?>
            <div id="submitReviewSection" style="background: var(--light-color); padding: 20px; border-radius: 10px;">
                <h4 style="margin-bottom: 10px; color: var(--dark-color);" id="submitReviewTitle">Rate this product</h4>
                <form id="ratingForm">
                    <input type="hidden" name="menu_item_id" id="ratingFormItemId">
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="font-weight: 500; display: block; margin-bottom: 5px;">Your Rating:</label>
                        <div class="star-rating-selector">
                            <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"><i class="fa-solid fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"><i class="fa-solid fa-star"></i></label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="ratingComment" style="font-weight: 500; display: block; margin-bottom: 5px;">Your Comment:</label>
                        <textarea id="ratingComment" name="comment" class="form-control" rows="3" placeholder="Explain your experience with this food/drink..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fa-solid fa-paper-plane"></i> Submit Review</button>
                </form>
            </div>
        <?php else: ?>
            <div style="background: var(--light-color); padding: 20px; border-radius: 10px; text-align: center;">
                <p style="margin: 0; color: var(--gray);">Please <a href="login.php" style="color: var(--primary-color); font-weight: 600;">login</a> to rate this item.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- jQuery (needed for simpler AJAX requests, optional but good for quick setup) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>
</body>

</html>