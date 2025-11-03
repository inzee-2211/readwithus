// assets/js/custom-web.js  (PATCHED)
$(function () {
  function initOwl() {
    if (!$.fn || !$.fn.owlCarousel) {
      // Owl not present yet — don’t crash the page
      // Optional: retry a couple of times if it loads late
      if (!initOwl._retries) initOwl._retries = 0;
      if (initOwl._retries < 10) {
        initOwl._retries++;
        return setTimeout(initOwl, 200); // try again in 200ms
      }
      console.warn('[custom-web] owlCarousel plugin not found. Skipping init.');
      return;
    }

    var $bestBuy = $('.best-buy');
    if ($bestBuy.length) {
      try {
        $bestBuy.owlCarousel({
          autoplay: false,
          autoplayTimeout: 5000,
          loop: true,
          margin: 20,
          nav: true,
          navText: [
            "<span class='custom-prev-icon'></span>",
            "<span class='custom-next-icon'></span>"
          ],
          dots: false,
          responsive: {
            0: { items: 1 },
            600: { items: 1 },
            1000: { items: 1 }
          }
        });
      } catch (e) {
        console.warn('[custom-web] best-buy init failed:', e);
      }
    }

    var $testimonials = $('.testimonial-slider');
    if ($testimonials.length) {
      try {
        $testimonials.owlCarousel({
          autoplay: false,
          autoplayTimeout: 5000,
          loop: true,
          margin: 20,
          nav: false,
          navText: [
            "<span class='custom-prev-icon'></span>",
            "<span class='custom-next-icon'></span>"
          ],
          dots: false,
          responsive: {
            0: { items: 1 },
            600: { items: 1 },
            1000: { items: 3 }
          }
        });
      } catch (e) {
        console.warn('[custom-web] testimonial init failed:', e);
      }
    }
  }

  initOwl();
});
