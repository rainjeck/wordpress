const form = {
    init() {
        if ( typeof Bouncer != 'function' ) return;

        this.antispam();

        this.inputTel();

        this.setup();
        this.validation();
        this.sending();
    },

    antispam() {
        let forms = document.forms;

        if ( !forms.length ) return;

        forms = Array.from(forms);

        forms.forEach(form => {
            const mouse = form.querySelector('input[name="mouse"]');

            if ( mouse ) {
                mouse.value = '';
            }

            const token = form.getAttribute('data-token');

            if ( token ) {
                let html = `<input type="hidden" name="token" value="${token}">`;
                form.insertAdjacentHTML('beforeend', html);
            }
        });
    },

    inputTel() {
        // https://github.com/uNmAnNeR/imaskjs
        // npm install imask

        if ( typeof IMask != 'function' ) return;

        const elems = document.querySelectorAll('.js-masked');

        if (!elems.length) return;

        elems.forEach((el) => {
            let mask = IMask(el, {
                mask: el.dataset.mask,
                // lazy: false
            });

            el.addEventListener('focus', (e) => {
                mask.updateOptions({ lazy: false });
            });

            el.addEventListener('blur', (e) => {
                mask.updateOptions({ lazy: true });
            });
        });
    },

    setup() {
        this.bouncerSettings = {
            disableSubmit: true,
            messageAfterField: true,
            messages: {
                missingValue: {
                    checkbox: 'Обязательное поле',
                    radio: 'Выберите значение',
                    select: 'Выберите значение',
                    'select-multiple': 'Выберите значение',
                    default: 'Обязательное поле',
                },
                patternMismatch: {
                    email: 'Не верный формат e-mail',
                    default: 'Проверьте формат значения',
                },
                phoneNum: 'Не верный формат телефона',
            },
            customValidations: {
                checkboxMultiValidator: (field) => {
                    if (field.type == 'checkbox' && field.name.includes('[]')) {
                        if (!field.required) return;

                        const fields = field.form.querySelectorAll(
                            'input[name="' + field.name + '"]'
                        );
                        const fieldsChecked = Array.from(fields).filter(
                            (item) => item.checked
                        );

                        if (fieldsChecked.length) {
                            fields.forEach((item) => {
                                item.classList.remove('error');
                                item.removeAttribute('required');
                            });
                            return false;
                        } else {
                            fields.forEach((item) => {
                                item.classList.add('error');
                                item.setAttribute('required', true);
                            });
                            return true; // error
                        }
                    }

                    // no error
                    return false;
                },
                phoneNum: (field) => {
                    if (field.type == 'tel') {
                        if (!field.required) return;
                        const pattern = /^\+7\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}$/;
                        let test = pattern.test(field.value);
                        if (!test) return true;
                    }

                    return false;
                },
            },
        };
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

            if (form.hasAttribute('method')) {
                form.submit();
                return;
            }

            const btn = form.querySelector('[type="submit"]');

            const url = '/wp-admin/admin-ajax.php';

            const fd = new FormData(form);

            fd.append('action', form.dataset.action);

            form.classList.add('is-loading');
            btn.setAttribute('disabled', true);

            fetch(url, {
                method: 'POST',
                body: fd,
            })
                .then((response) => response.json())
                .then((res) => {
                    form.classList.remove('is-loading');
                    btn.removeAttribute('disabled');
                    form.reset();

                    // metrika in Options
                    if ( typeof fireball == 'function' ) {
                        fireball();
                    }

                    if ( res.data.modal ) {
                        Unimodal.open(res.data.modal);
                    }

                    if (res.data.url) {
                        window.location.assign(res.data.url);
                    }

                    if (res.success) {
                        form.reset();
                        form.classList.add('is-success');

                        setTimeout(() => {
                            form.classList.remove('is-success');
                            // Unimodal.closeAll();
                        }, 3000);
                    }

                    if (!res.success) {
                        form.classList.add('is-error');

                        setTimeout(() => {
                            form.classList.remove('is-error');
                        }, 3000);

                        console.error(res);
                        return;
                    }
                });
        }, false);
    },
};

export default form;
