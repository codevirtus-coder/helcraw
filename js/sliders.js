document.addEventListener('DOMContentLoaded', function () {
    const swiperHomeBanner = new Swiper('.homeSlider', {
        initialSlide: 0,
        loop: true,
        speed: 800,
        grabCursor: true,
        slidesPerView: 1,
        navigation: {
            nextEl: '.home-banner-next', // Use the new custom class
            prevEl: '.home-banner-prev', // Use the new custom class
        },
        keyboard: {
            enabled: true,
        },
    });
});