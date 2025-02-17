const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggle-btn');
const overlay = document.getElementById('overlay');

$(document).on('sideBarToggle', function() {
  console.log('sideBarToggle');
  toggleCollapsed();
});

if (localStorage.getItem('sidebar-collapsed') === 'true') {
  sidebar.classList.add('collapsed');
}

function toggleCollapsed() {
  if (window.innerWidth > 768) {
    sidebar.classList.toggle('collapsed');
    console.log(sidebar.classList.contains('collapsed'));
    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
  } else {
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
  }
}

toggleBtn.addEventListener('click', (event) => {
  event.preventDefault();
  toggleCollapsed();
});

overlay.addEventListener('click', closeMobileSidebar);

document.querySelectorAll('.menu-item.no-submenu').forEach(item => {
  item.addEventListener('click', (event) => {
    event.preventDefault();
    setActive(item);
    closeMobileSidebar();
  });
});

document.querySelectorAll('.submenu-item').forEach(item => {
  item.addEventListener('click', (event) => {
    event.preventDefault();
    setActive(item);
    closeMobileSidebar();
  });
});

document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(item => {
  const chevron = item.querySelector('.chevron');

  item.addEventListener('click', function () {
    chevron.classList.toggle('rotate');

    document.querySelectorAll('.submenu.show').forEach(openMenu => {
      if (openMenu.id !== this.getAttribute('href').substring(1)) {
        new bootstrap.Collapse(openMenu, {
          toggle: false
        }).hide();
        openMenu.previousElementSibling.querySelector('.chevron').classList.remove('rotate');
      }
    });
  });
});

function setActive(element) {
  document.querySelectorAll('.menu-item, .submenu-item').forEach(item => {
    item.classList.remove('active');
  });
  element.classList.add('active');
}

function closeMobileSidebar() {
  if (window.innerWidth <= 768) {
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
  }
}