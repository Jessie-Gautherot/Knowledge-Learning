document.addEventListener("DOMContentLoaded", () => {

    const buttons = document.querySelectorAll('.menu-btn');

    // MENU DROPDOWN
    buttons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const item = button.parentElement;

            document.querySelectorAll('.menu-item').forEach(el => {
                if (el !== item) {
                    el.classList.remove('active');
                }
            });

            item.classList.toggle('active');
        });
    });

    // FERMER SI CLIC EXTERIEUR
    document.addEventListener('click', () => {
        document.querySelectorAll('.menu-item').forEach(el => {
            el.classList.remove('active');
        });
    });

    //fermeture aorès click
    document.querySelectorAll('.submenu a').forEach(link => {
    link.addEventListener('click', () => {
        document.querySelectorAll('.menu-item').forEach(el => {
            el.classList.remove('active');
        });
    });
});

});
