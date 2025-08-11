<?php
require 'function.php';
require 'cek.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <style>
.hologram-text {
  font-weight: 900;
  font-size: 2.5rem;
  color: #383c3cff;
  text-align: center;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  text-shadow:
    0 0 5px #888888,
    0 0 10px #aaaaaa,
    0 0 20px #bbbbbb,
    0 0 30px #cccccc;
  animation: flicker 3s infinite alternate;
}
@keyframes flicker {
  0%, 19%, 21%, 23%, 25%, 54%, 56%, 100% {
    opacity: 1;
  }
  20%, 22%, 24%, 55% {
    opacity: 0.5;
  }
}
#calendar {
  max-width: 100%;
  margin: 0 auto;
  padding: 20px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    selectable: true,
    dateClick: function(info) {
      const title = prompt('Masukkan Nama Event Booking:');
      if (title) {
        calendar.addEvent({
          title: title,
          start: info.dateStr,
          allDay: true
        });
      }
    }
  });
  calendar.render();
});
</script>
<!-- ... Tag <head> lainnya ... -->
</head>
<body class="sb-nav-fixed">
<!-- ... Navbar dan Sidebar tetap sama ... -->

<!-- Dashboard 4 -->
<section id="dashboard4" class="mb-5">
  <h4 class="mb-4 text-center">Jadwal Booking Event</h4>
  <div id='calendar'></div>
</section>

<script>
function showDashboard(id) {
    const sections = ['dashboard1', 'dashboard2', 'dashboard3', 'dashboard4'];
    sections.forEach(section => {
        const el = document.getElementById(section);
        if (el) el.style.display = (section === id) ? 'block' : 'none';
    });
}
</script>

<!-- Tambahkan link ke sidebar -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebarNav = document.querySelector('.sb-sidenav .nav');
  const link = document.createElement('a');
  link.className = 'nav-link';
  link.href = '#';
  link.setAttribute('onclick', "showDashboard('dashboard4')");
  link.innerHTML = `<div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>Booking Event`;
  sidebarNav.appendChild(link);

  // Default ke dashboard1
  showDashboard('dashboard1');
});
</script>

<!-- ... Footer dan script tetap sama ... -->
</body>
</html>
