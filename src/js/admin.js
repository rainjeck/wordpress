(function () {
  'use strict';

  const app = {
    init() {
      this.createBackupFile();
      this.removeBackupFile();

      this.regenerateThumbsButton();
    },

    createBackupFile() {
      const el = document.querySelector('#create-backup');

      if (!el) return;

      el.addEventListener('click', e => {
        const url = `/wp-admin/admin-ajax.php`;

        const fd = new FormData();

        fd.append('action', 'createBackupFile');
        fd.append('token', el.dataset.token);

        form.classList.add('is-loading');

        fetch(url, {
          method: 'POST',
          body: fd
        }).then(response => response.json()).then(res => {
          if (res.success) {
            window.location.reload();
          }
        });
      });
    },

    removeBackupFile() {
      const elems = document.querySelectorAll('.js-backup-delete');

      if (!elems.length) return;

      const url = `/wp-admin/admin-ajax.php`;

      const fd = new FormData();

      fd.append('action', 'deleteBackupFile');

      elems.forEach(el => {
        el.addEventListener('click', e => {
          fd.append('token', el.dataset.token);
          fd.append('file', el.dataset.file);

          form.classList.add('is-loading');

          fetch(url, {
            method: 'POST',
            body: fd
          }).then(response => response.json()).then(res => {
            if (res.success) {
              window.location.reload();
            }
          });
        });
      });
    },

    regenerateThumbsButton() {
      if ( !document.querySelector('#regenerate-thumbs-btn') ) return;

      let status = false;

      const btn = document.querySelector('#regenerate-thumbs-btn');

      btn.addEventListener('click', e => {
        status = true;

        const url = `/wp-admin/admin-ajax.php`;

        const fd = new FormData();

        fd.append('action', 'regenerateThumbs');
        fd.append('token', btn.dataset.token);

        const fdStatus = new FormData();
        fdStatus.append('action', 'regenerateThumbsStatus');

        const int = setInterval(() => {
          if (status) {
            fetch(url, {
              method: 'POST',
              body: fdStatus
            }).then(response => response.json()).then(res => {
              document.querySelector('#images_status').value = res.data;
            });
          } else {
            clearInterval(int);
            fetch(url, {
              method: 'POST',
              body: fdStatus
            }).then(response => response.json()).then(res => {
              document.querySelector('#images_status').value = res.data;
            });
          }
        }, 1000);

        fetch(url, {
          method: 'POST',
          body: fd
        }).then(response => response.json()).then(data => {
          status = false;
        });
      });
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    app.init();
  });
}());
