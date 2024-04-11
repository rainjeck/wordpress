const app = {
    init() {
        // svg4everybody();

        console.log("It works!");

        // this.toggler();
        // this.modal();
        // this.gallery();
        // this.modal();
        // this.yamap();
    },

    blockScroll(status = false) {
        if (!status) {
            document.documentElement.classList.remove("is-no-scroll");
            return;
        }

        document.documentElement.classList.add("is-no-scroll");
    },

    toggler() {
        new Unitoggle({
            onOpen: tab => {}, // After open callback
            onClose: tab => {}, // After close callback
        });
    },

    modal() {
        new Unimodal({
            onOpen: (modal, button) => {},
            onClose: modal => {},
        });
    },

    gallery() {
        let elems = document.querySelectorAll(".js-gallery");

        if (!elems.length) return;

        elems.forEach((el) => {
            lightGallery(el, {
                selector: ".js-gallery-link",
                download: false,
                getCaptionFromTitleOrAlt: true,
                // appendSubHtmlTo: '.lg-item',
                subHtmlSelectorRelative: true,
            });
        });
    },

    yamap() {
        const elems = document.querySelectorAll(".js-map");

        if (!elems.length) return;

        ymaps.ready(() => {
            elems.forEach((el) => {
                let coords = JSON.parse(el.dataset.coords);
                // let mark = JSON.parse(el.dataset.mark);

                let myMap = new ymaps.Map(el.id, {
                    center: coords, // Координаты центра карты
                    zoom: 14, // от 0 (весь мир) до 19
                    controls: [
                        "typeSelector",
                        "fullscreenControl",
                        "zoomControl",
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
