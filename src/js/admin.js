import axios from 'axios';

class App {
  constructor() {
    this.regenerateThumbsButton();
  }

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
        if ( status ) {
          axios.post(url, fdStatus).then( res => {
            document.querySelector('#images_status').value = res.data.data;
          });
        } else {
          clearInterval(int);
        }
      }, 1000);

      axios.post(url, fd).then( res => {
        status = false;
      });
    });
  }
};

new App();
