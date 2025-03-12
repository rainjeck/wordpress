import svg4everybody from 'svg4everybody';
import Bouncer from 'formbouncerjs';
// import IMask from 'imask';
// import SwiperCore, { Navigation, Pagination } from 'swiper/core';
// import lightGallery from 'lightgallery.js';

// SwiperCore.use([Navigation, Pagination]);

// import Unitoggle from '../libs/unitoggle.js';
// import Unimodal from '../libs/unimodal.js';

window.svg4everybody = svg4everybody;
window.Bouncer = Bouncer;
// window.IMask = IMask;
// window.Swiper = SwiperCore;

import nospam from './module/nospam';
import form from './module/form';

(() => {
    'use strict';

    const module = {
        init() {
            console.log("It works!");
            // svg4everybody();

            // this.toggler();
            // this.modal();
            // this.gallery();
            // this.modal();
            // this.slider();
            // this.yamap();
        },

        blockScroll(status = false) {
            if (!status) {
                document.documentElement.classList.remove('is-no-scroll');
                return;
            }

            document.documentElement.classList.add('is-no-scroll');
        },

        toggler() {
            new Unitoggle({
                onOpen: tab => {}, // After open callback
                onClose: tab => {}, // After close callback
            });
        },

        modal() {
            new Unimodal({
                onOpen: (modal, btn) => {},
                onClose: modal => {},
            });
        },

        gallery() {
            let elems = document.querySelectorAll('.js-gallery');

            if (!elems.length) return;

            elems.forEach((el) => {
                lightGallery(el, {
                    selector: '.js-gallery-link',
                    download: false,
                    getCaptionFromTitleOrAlt: true,
                    // appendSubHtmlTo: '.lg-item',
                    subHtmlSelectorRelative: true,
                });
            });
        },

        slider() {
            const elems = document.querySelectorAll('.js-slider');

            if ( !elems.length ) return;

            elems.forEach(el => {
                const sl = el.querySelector('.js-sl');

                const prev = el.querySelector('.js-prev');
                const next = el.querySelector('.js-next');
                const pagin = el.querySelector('.js-pagin');
                const autoplay = sl.getAttribute('data-autoplay');

                new Swiper(sl, {
                    slidesPerView: 'auto',
                    autoplay: (autoplay) ? {
                        delay: autoplay,
                    } : false,
                    navigation: (prev && next) ? {
                        prevEl: prev,
                        nextEl: next,
                        disabledClass: 'is-disabled',
                        lockClass: 'is-lock',
                    } : false,
                    pagination: (pagin) ? {
                        el: pagin,
                        type: 'bullets',
                        bulletActiveClass: 'is-active',
                        bulletClass: 'bullet',
                        currentClass: 'is-current',
                    } : false,
                });
            });
        },

        yamap() {
            const elems = document.querySelectorAll('.js-map');

            if (!elems.length) return;

            ymaps.ready(() => {
                elems.forEach((el) => {
                    let coords = JSON.parse(el.dataset.coords);
                    // let mark = JSON.parse(el.dataset.mark);

                    let myMap = new ymaps.Map(el.id, {
                        center: coords, // Координаты центра карты
                        zoom: 14, // от 0 (весь мир) до 19
                        controls: [
                            'typeSelector',
                            'fullscreenControl',
                            'zoomControl',
                        ],
                    });

                    let myPlacemark = new ymaps.Placemark(
                        coords,
                        {
                            // balloonContent: el.dataset.txt
                        },
                        {
                            // iconLayout: 'default#image',
                            // iconImageHref: el.dataset.mark,
                            // iconImageSize: [32, 32],
                            // iconImageOffset: [-16, -32]
                        }
                    );

                    myMap.geoObjects.add(myPlacemark);
                });
            });
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        module.init();
        nospam.init();
        form.init();
    });
})();
