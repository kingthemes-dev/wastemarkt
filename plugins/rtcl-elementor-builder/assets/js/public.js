/******/ (() => { // webpackBootstrap
/*!**************************!*\
  !*** ./src/js/public.js ***!
  \**************************/
(function ($, window) {
  $(".rtcl.store-content-wrap .rtcl-block-frontend .store-email-label").click(function () {
    $("#store-email-area").toggle();
  });

  //swiper slider intialize
  window.initRtclSlider = function () {
    var swiperMainWrapper = $(".rtcl-block-frontend.rtcl-listings-sc-wrapper");
    if (swiperMainWrapper.length) {
      $(".rtcl-carousel-slider").each(function (i) {
        var $thisSlider = $(this);
        setTimeout(function () {
          $thisSlider.parents('.rtcl-listings-wrapper').animate({
            opacity: "1"
          });
          $thisSlider.parents('.rtcl-listings-wrapper').parents('.rtcl-listings-sc-wrapper').addClass('rtcl-swiper-init');
        }, 100);
      });
    }
  };
  setTimeout(initRtclSlider, 500);
})(jQuery, window);
/******/ })()
;