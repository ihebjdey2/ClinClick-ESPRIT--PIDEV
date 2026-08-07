(() => {
  'use strict';

  const sidebarToggle = document.querySelector('.toggle-sidebar-btn');
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
      const isCollapsed = document.body.classList.toggle('toggle-sidebar');
      sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
    });
  }

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
    bootstrap.Tooltip.getOrCreateInstance(element);
  });

  document.querySelectorAll('.needs-validation').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add('was-validated');
    });
  });
})();
