document.addEventListener('DOMContentLoaded', () => {
    /* ── Navbar scroll effect ── */
    const nav = document.getElementById('mainNav');
    if (nav) {
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });
    }

    /* ── Scroll reveal ── */
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length > 0) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12 });
        revealEls.forEach(el => io.observe(el));
    }

    /* ── Animated counters ── */
    function animateCount(el, target, duration = 1600) {
        let start = null;
        const step = ts => {
            if (!start) start = ts;
            const progress = Math.min((ts - start) / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 4);
            el.textContent = Math.floor(ease * target);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target;
        };
        requestAnimationFrame(step);
    }

    const statsIo = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const t = parseInt(e.target.dataset.target, 10);
                if (!isNaN(t)) {
                    animateCount(e.target, t);
                }
                statsIo.unobserve(e.target);
            }
        });
    }, { threshold: 0.5 });

    // Get values from window object (passed from Blade template)
    const countMappings = [
        { id: 'count-total',     val: window.statsData?.total || 0 },
        { id: 'count-ongoing',   val: window.statsData?.ongoing || 0 },
        { id: 'count-completed', val: window.statsData?.completed || 0 },
    ];
    
    countMappings.forEach(({ id, val }) => {
        const el = document.getElementById(id);
        if (el) { 
            el.dataset.target = val; 
            statsIo.observe(el); 
        }
    });

    /* ── Progress bar animation on load ── */
    window.addEventListener('load', () => {
        setTimeout(() => {
            const pb = document.getElementById('heroProgressBar');
            if (pb && window.statsData?.completionRate !== undefined) {
                pb.style.width = window.statsData.completionRate + '%';
            }
        }, 800);
    });

    /* ── Smooth anchor scroll ── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
});