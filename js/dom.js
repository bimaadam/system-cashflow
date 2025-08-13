function showDashboard(id) {
    const sections = ['dashboard1', 'dashboard2', 'dashboard3','dashboard4','dashboard5'];
    sections.forEach(section => {
        const el = document.getElementById(section);
        if (el) el.style.display = (section === id) ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'dashboard1';
    showDashboard(activeTab);
});