(function () {
  const app = {
    init() {
      const _this = app;

      _this.regenerateThumbsButton();
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
