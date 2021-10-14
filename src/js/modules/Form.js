class Form {

  constructor() {
    this.setup();
    this.validation();
    this.sending();
  }

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
  }

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
  }

  sending() {
    document.addEventListener('bouncerFormValid', e => {
      const form = e.target;
      const type = form.dataset.type;

      const btn = form.querySelector('[type="submit"]');

      const url = `${form.action}wp-admin/admin-ajax.php`;

      const fd = new FormData(form);

      fd.append('action', 'mail');

      form.classList.add('is-process');
      btn.setAttribute('disabled', true);

      axios.post(url, fd).then((res) => {
        console.log(res.data);

        if (res.data.success) {
          form.classList.remove('is-process');
          btn.removeAttribute('disabled');
          form.reset();

          if (res.data.data.url) {
            window.location.assign(res.data.data.url);
          }
        }
      });
    }, false);
  }
};

export default Form;
