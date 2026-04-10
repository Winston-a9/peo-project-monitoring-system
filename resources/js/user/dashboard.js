// ═══ DONUT HOVER FUNCTIONALITY ═══
const center = document.getElementById('donut-center');
if (center) {
    document.querySelectorAll('.donut-segment').forEach(seg => {
        seg.addEventListener('mouseenter', () => {
            center.innerHTML = `
                        <span style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:var(--text-primary);line-height:1;">${seg.dataset.count}</span>
                        <span style="font-size:0.6rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-top:2px;">${seg.dataset.label}</span>
                        <span style="font-size:0.65rem;font-weight:700;color:var(--text-secondary);">${seg.dataset.pct}%</span>`;
        });
        seg.addEventListener('mouseleave', () => {
            center.innerHTML = `
                        <span style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:var(--text-primary);line-height:1;">{{ $total }}</span>
                        <span style="font-size:0.65rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Total</span>`;
        });
    });
}