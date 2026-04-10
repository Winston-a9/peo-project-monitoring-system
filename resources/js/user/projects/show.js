function toggleLog(id) {
    const el = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    const isOpen = el.style.display === 'flex';
    el.style.display = isOpen ? 'none' : 'flex';
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}