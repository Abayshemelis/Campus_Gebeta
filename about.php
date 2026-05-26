<?php
require_once 'includes/header.php';
?>

<div class="container" style="padding: 60px 0;">
    <!-- Project Objective Section -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 20px; color: var(--dark-color);">Project Objective</h1>
        <div style="width: 60px; height: 4px; background: var(--primary-color); margin: 0 auto 30px; border-radius: 2px;"></div>
        <p style="font-size: 1.2rem; line-height: 1.8; color: var(--gray);">
            Our mission is to <strong>increase brand recognition</strong>, help our platform <strong>stand out</strong>, and connect with our customers on a deep <strong>emotional level</strong>. We believe in providing an exceptional, seamless experience for every university student.
        </p>
    </div>

    <!-- Provided Services Section -->
    <div style="margin-top: 80px;">
        <h2 style="text-align: center; font-size: 2rem; margin-bottom: 40px; color: var(--dark-color);">Provided Services</h2>

        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <!-- Service 1 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(255, 107, 107, 0.1); color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Brand Strategy</h3>
                <p style="color: var(--gray); line-height: 1.6;">Crafting unique market positioning and compelling brand narratives to connect with your target audience effectively.</p>
            </div>

            <!-- Service 2 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(252, 163, 17, 0.1); color: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Visual Identity</h3>
                <p style="color: var(--gray); line-height: 1.6;">Designing memorable logos, color palettes, and typography systems that make your brand instantly recognizable.</p>
            </div>

            <!-- Service 3 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(43, 45, 66, 0.1); color: var(--dark-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Brand Application</h3>
                <p style="color: var(--gray); line-height: 1.6;">Applying your core brand identity across all physical and digital touchpoints for maximum consistency.</p>
            </div>

            <!-- Service 4 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(78, 168, 222, 0.1); color: #4ea8de; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Website Design</h3>
                <p style="color: var(--gray); line-height: 1.6;">Creating stunning, user-friendly, and responsive web experiences tailored to modern digital standards.</p>
            </div>

            <!-- Service 5 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(155, 89, 182, 0.1); color: #9b59b6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-video"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Motion Design</h3>
                <p style="color: var(--gray); line-height: 1.6;">Bringing your brand to life with dynamic animations, transitions, and eye-catching visual effects.</p>
            </div>

            <!-- Service 6 -->
            <div class="card" style="text-align: center; padding: 40px 30px; border-radius: 12px; transition: transform 0.3s, box-shadow 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow)';">
                <div style="width: 80px; height: 80px; background: rgba(46, 204, 113, 0.1); color: #2ecc71; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px;">
                    <i class="fa-solid fa-code"></i>
                </div>
                <h3 style="margin-bottom: 15px; color: var(--dark-color);">Development</h3>
                <p style="color: var(--gray); line-height: 1.6;">Building robust, scalable, and secure backend architectures and front-end implementations.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>