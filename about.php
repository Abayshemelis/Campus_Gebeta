<?php
require_once 'includes/header.php';
?>

<div class="container" style="padding: 60px 0;">
    <!-- Project Objective Section -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
        <h1 style="font-size: 2.8rem; margin-bottom: 20px; color: var(--dark-color);"><i class="fa-solid fa-circle-info" style="color:var(--primary-color);"></i> About Campus Gebeta</h1>
        <div style="width: 60px; height: 4px; background: var(--primary-color); margin: 0 auto 30px; border-radius: 2px;"></div>
        <p style="font-size: 1.25rem; line-height: 1.8; color: var(--gray);">
            Campus Gebeta is a state-of-the-art campus meal ordering platform designed specifically for university students and sellers. Our goal is simple: <strong>skip the queue, order meals online, and save precious student time</strong>.
        </p>
    </div>

    <!-- Core Services/Features Section -->
    <div style="margin-top: 80px;">
        <h2 style="text-align: center; font-size: 2.2rem; margin-bottom: 40px; color: var(--dark-color);">Our Platform Features</h2>

        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <!-- Feature 1 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(255, 107, 107, 0.1); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Instant Ordering</h3>
                <p style="color: var(--gray); line-height: 1.6;">Browse campus menus, customize your food, and complete order checkout in a few clicks.</p>
            </div>

            <!-- Feature 2 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(252, 163, 17, 0.1); color: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Zero Queue Time</h3>
                <p style="color: var(--gray); line-height: 1.6;">Order from your dorm room or classroom, and walk to the counter only when your food is ready.</p>
            </div>

            <!-- Feature 3 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(43, 45, 66, 0.1); color: var(--dark-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-shop"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Seller Dashboard</h3>
                <p style="color: var(--gray); line-height: 1.6;">Cafeterias and individual campus shops can manage their menus, update item availability, and track incoming orders live.</p>
            </div>

            <!-- Feature 4 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(78, 168, 222, 0.1); color: #4ea8de; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Student Reviews</h3>
                <p style="color: var(--gray); line-height: 1.6;">A completely transparent rating system that helps students identify top-rated meals and providers on campus.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>