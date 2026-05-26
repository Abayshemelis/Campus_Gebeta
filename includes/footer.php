</main> <!-- Close main-content -->

<footer class="footer" style="background-color: var(--dark-color); color: var(--light-color); padding: 50px 0 20px; margin-top: 50px;">
    <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px; margin-bottom: 30px;">
        <!-- Brand Section -->
        <div class="footer-brand" style="flex: 1; min-width: 250px;">
            <a href="index.php" style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; text-decoration: none;">
                <img src="assets/images/Gebeta.png" alt="Campus Gebeta Logo" style="height: 40px; border-radius: 5px;">
                <span style="color: var(--white); font-size: 1.5rem; font-weight: 700;">Campus <span style="color: var(--primary-color);">Gebeta</span></span>
            </a>
            <p style="color: #aaa; line-height: 1.6; margin-bottom: 20px;">
                Digitizing campus dining and life for faster, smarter, and better service. The ultimate super-app for university students in Ethiopia.
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
                <li><a href="market.php" style="color: #aaa; text-decoration: none; transition: var(--transition);"><i class="fa-solid fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i> Gebeta Market</a></li>
                <li><a href="noticeboard.php" style="color: #aaa; text-decoration: none; transition: var(--transition);"><i class="fa-solid fa-angle-right" style="color: var(--primary-color); margin-right: 8px;"></i> Noticeboard</a></li>
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

<!-- jQuery (needed for simpler AJAX requests, optional but good for quick setup) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>
</body>

</html>