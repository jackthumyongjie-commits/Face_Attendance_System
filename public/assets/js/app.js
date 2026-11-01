document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  const path = window.location.pathname;
  document.querySelectorAll('.sidebar-nav .nav-link').forEach((link) => {
    try {
      const href = new URL(link.href).pathname;
      if (path === href || path.startsWith(href + '/')) {
        link.classList.add('active');
      }
    } catch (e) {}
  });
});

window.AppApi = {
  base() {
    return window.APP_BASE || '';
  },
  csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  },
  async get(path) {
    const res = await fetch(this.base() + path, {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    });
    return res.json();
  },
  async post(path, body) {
    const res = await fetch(this.base() + path, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': this.csrf(),
      },
      credentials: 'same-origin',
      body: JSON.stringify({ ...body, _token: this.csrf() }),
    });
    return res.json();
  },
};
