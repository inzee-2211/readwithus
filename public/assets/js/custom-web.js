$(document).ready(function () {
   
    $('.best-buy').owlCarousel({
        autoplay:false,
        autoplayTimeout: 5000,
        loop:true,
        margin:20,
        nav:true,
       
        navText: [
            "<span class='custom-prev-icon'></span>",
            "<span class='custom-next-icon'></span>"
        ],
        dots:false,
        responsive:{
            0:{ items:1 },
            600:{ items:1 },
            1000:{ items:1 }
        }
    });

    $('.testimonial-slider').owlCarousel({
        autoplay: false,
        autoplayTimeout: 5000,
        loop:true,
        margin:20,
        nav:false,
        
        navText: [
            "<span class='custom-prev-icon'></span>",
            "<span class='custom-next-icon'></span>"
        ],
        dots:false,
        responsive:{
            0:{ items:1 },
            600:{ items:1 },
            1000:{ items:3 }
        }
    });
});
