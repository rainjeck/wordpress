const form = {
  init() {
    // this.inputTel();

    this.setup();
    this.validation();
    this.sending();
  },

  inputTel() {
    // https://github.com/uNmAnNeR/imaskjs
    const elems = document.querySelectorAll('.js-masked');

    if (!elems.length) return;

    elems.forEach(el => {
      let mask = IMask(el, {
        mask: el.dataset.mask
        // lazy: false
      });

      el.addEventListener('focus', e => {
        mask.updateOptions({ lazy: false });
      });

      el.addEventListener('blur', e => {
        mask.updateOptions({ lazy: true });
      });
    });
  },

  setup() {
    this.bouncerSettings = {
      messageAfterField: true,
      messages: {
        missingValue: {
          checkbox: 'Обязательное поле',
          radio: 'Выберите значение',
          select: 'Выберите значение',
          'select-multiple': 'Выберите значение',
          default: 'Обязательное поле'
        },
        patternMismatch: {
          email: 'Не верный формат e-mail',
          default: 'Проверьте формат значения'
        }
      },
      disableSubmit: true
    }
  },

  validation() {
    if (!document.querySelector('[data-bouncer]')) return;

    const bouncer = new Bouncer('[data-bouncer]', this.bouncerSettings);

    document.addEventListener('bouncerRemoveError', e => {
      var field = e.target;
      field.classList.add('valid');
    }, false);

    document.addEventListener('bouncerShowError', e => {
      var field = e.target;
      field.classList.remove('valid');
    }, false);
  },

  sending() {
    document.addEventListener('bouncerFormValid', e => {
      const form = e.target;
      const type = form.dataset.type;

      if (form.hasAttribute('method')) {
        form.submit();
        return;
      }

      const btn = form.querySelector('[type="submit"]');

      const url = `${form.action}wp-admin/admin-ajax.php`;

      const fd = new FormData(form);

      fd.append('action', 'mail');

      form.classList.add('is-process');
      btn.setAttribute('disabled', true);

      fetch(url, {
        method: 'POST',
        body: fd
      }).then(response => response.json()).then(res => {
        console.log(res);

        form.classList.remove('is-process');
        btn.removeAttribute('disabled');
        form.reset();

        if (res.data.url) {
          window.location.assign(res.data.url);
        }
      });
    }, false);
  }
};
