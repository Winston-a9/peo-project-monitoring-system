// ═══ DONUT HOVER FUNCTIONALITY WITH ANIMATIONS ═══
const center = document.getElementById('donut-center');
if (center) {
    // Store the default HTML
    const defaultHTML = `
        <span style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-primary);line-height:1;">${window.dashboardTotal}</span>
        <span style="font-size:0.65rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Total</span>`;

    // Set it immediately on load
    center.innerHTML = defaultHTML;

    document.querySelectorAll('.donut-segment').forEach(seg => {
        seg.addEventListener('mouseenter', () => {
            // Add smooth fade animation
            center.style.animation = 'none';
            setTimeout(() => {
                center.style.animation = 'fadeUp 0.2s ease both';
                center.innerHTML = `
                    <span style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:var(--text-primary);line-height:1;">${seg.dataset.count}</span>
                    <span style="font-size:0.6rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-top:2px;">${seg.dataset.label}</span>
                    <span style="font-size:0.65rem;font-weight:700;color:var(--text-secondary);">${seg.dataset.pct}%</span>`;
            }, 10);
            
            // Highlight the segment
            seg.classList.add('active');
        });
        
        seg.addEventListener('mouseleave', () => {
            center.style.animation = 'none';
            setTimeout(() => {
                center.style.animation = 'fadeUp 0.2s ease both';
                center.innerHTML = defaultHTML;
            }, 10);
            
            // Remove highlight
            seg.classList.remove('active');
        });
    });
}

// ═══ STAT CARD INTERACTIVE ANIMATIONS ═══
document.querySelectorAll('.stat-card').forEach((card, index) => {
    // Add staggered entrance animation
    card.style.animationDelay = `${index * 0.05}s`;
    
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-6px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// ═══ RECENT ROW INTERACTIONS ═══
document.querySelectorAll('.recent-row').forEach(row => {
    row.addEventListener('mouseenter', function() {
        this.style.opacity = '1';
    });
    
    row.addEventListener('mouseleave', function() {
        this.style.opacity = '0.95';
    });
    
    // Smooth scroll to top on click
    row.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});

// ═══ QUICK LINK INTERACTIVE ANIMATIONS ═══
document.querySelectorAll('.quick-link').forEach(link => {
    const icon = link.querySelector('i:last-child');
    
    link.addEventListener('mouseenter', function() {
        if (icon) {
            icon.style.transform = 'translateX(3px) scale(1.1)';
        }
    });
    
    link.addEventListener('mouseleave', function() {
        if (icon) {
            icon.style.transform = 'translateX(0) scale(1)';
        }
    });
});

// ═══ SCROLL REVEAL ANIMATIONS ═══
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe cards for lazy animation
document.querySelectorAll('.card, .stat-card').forEach(card => {
    card.style.opacity = '0.8';
    card.style.transform = 'translateY(10px)';
    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(card);
});

// ═══ SMOOTH TRANSITIONS FOR BADGES ═══
document.querySelectorAll('.badge').forEach(badge => {
    badge.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.08)';
        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    });
    
    badge.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = 'none';
    });
});

// ═══ RIPPLE EFFECT ON CARD CLICK ═══
function createRipple(event) {
    const button = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    button.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

document.querySelectorAll('.stat-card').forEach(card => {
    card.addEventListener('click', createRipple);
});

// ═══ ANIMATED COUNTER FOR STATS ═══
function animateCounter(element, duration = 1000) {
    const finalValue = parseInt(element.textContent);
    const increment = finalValue / (duration / 16);
    let currentValue = 0;
    
    const counter = setInterval(() => {
        currentValue += increment;
        if (currentValue >= finalValue) {
            element.textContent = finalValue;
            clearInterval(counter);
        } else {
            element.textContent = Math.floor(currentValue);
        }
    }, 16);
}

// Trigger counters on page load or scroll into view
document.querySelectorAll('.stat-count').forEach((counter, index) => {
    setTimeout(() => {
        animateCounter(counter, 800);
    }, index * 100);
});

// ═══ PARALLAX EFFECT ON SCROLL ═══
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    document.querySelectorAll('.stat-card').forEach(card => {
        const offset = scrolled * 0.05;
        card.style.transform = `translateY(${offset}px)`;
    });
});

// ═══ KEYBOARD NAVIGATION ═══
document.querySelectorAll('.stat-card, .quick-link, .recent-row').forEach(element => {
    element.setAttribute('tabindex', '0');
    
    element.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            element.click();
        }
    });
});

// ═══ RESPONSIVE ADJUSTMENTS ═══
function adjustForMobile() {
    const isMobile = window.innerWidth < 768;
    document.querySelectorAll('.stat-card').forEach(card => {
        if (isMobile) {
            card.style.transition = 'transform 0.15s ease';
        } else {
            card.style.transition = 'transform 0.3s ease';
        }
    });
}

window.addEventListener('resize', adjustForMobile);
adjustForMobile();

// ═══ TOOLTIP SUPPORT ═══
document.querySelectorAll('[data-tooltip]').forEach(element => {
    element.addEventListener('mouseenter', function() {
        const tooltip = document.createElement('div');
        tooltip.textContent = this.dataset.tooltip;
        tooltip.style.cssText = `
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 1000;
            pointer-events: none;
            animation: fadeUp 0.2s ease both;
        `;
        document.body.appendChild(tooltip);
        
        const rect = this.getBoundingClientRect();
        tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
        
        this.tooltip = tooltip;
    });
    
    element.addEventListener('mouseleave', function() {
        if (this.tooltip) {
            this.tooltip.remove();
            this.tooltip = null;
        }
    });
});

// ═══ PAGE LOAD COMPLETE ANIMATION ═══
window.addEventListener('load', () => {
    document.querySelectorAll('.fade-up, .fade-up-2, .fade-up-3').forEach((el, idx) => {
        el.style.opacity = '1';
    });
});