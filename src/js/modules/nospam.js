const nospam = {
    init() {
        this.checkMobile();

        this.convertTelNumber();
        this.convertEmail();
    },

    checkMobile() {
        let is_mobile = navigator.userAgent.toLowerCase().includes('mobile');

        if ( is_mobile ) {
            this.is_mobile = true;
            return;
        }

        this.is_mobile = false;
    },

    convertTelNumber() {
        if ( !this.is_mobile ) return;

        const elems = document.querySelectorAll('.js-tel-mobile');

        if ( !elems.length ) return;

        elems.forEach(el => {
            let number = el.getAttribute('data-tel');
            let tel = el.innerHTML;

            let html = `<a href='tel:${number}' class='link'>${tel}</a>`;

            el.innerHTML = html;
        });
    },

    convertEmail() {
        const elems = document.querySelectorAll('.js-mail');

        if ( !elems.length ) return;

        elems.forEach(el => {
            let email = el.getAttribute('data-mail');

            let html = `<a href='mailto:${email}' class='link'>${email}</a>`;

            el.innerHTML = html;
        });
    },
};
