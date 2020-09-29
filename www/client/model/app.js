// Když je stránka načtena
$(document).ready(function() {
    // Zakryje clonu
    $('#cover').fadeOut();

    // Logika responzivního menu
    $('#menu').on('click', function(e) {
        // Přidá .open třídu do hamburger menu - otevře menu
        $('#menu-items').toggleClass('open');

        // Animace menu
        $(this).toggleClass('menu-animation');
    });

    // Změna šířky okna
    $(window).resize(function() {
        // Zavře menu při změně šířky a odebre animační třídu
        if (!window.matchMedia('(max-width: 768px)').matches) {
            // Odebere animační třídu
            $('#menu').removeClass('menu-animation');

            // Uzavře menu - odebere třídu
            $('.menu-items').removeClass('open');
        }
    });
});
