document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById("loginModal");
    const btnLogin = document.getElementById("btn-login");
    const btnClose = document.querySelector(".close-btn");

    // 1. Abre el modal al hacer clic en "Iniciar Sesión"
    btnLogin.addEventListener("click", () => {
        modal.classList.add("show");
    });

    // 2. Cierra el modal al hacer clic en la "X"
    btnClose.addEventListener("click", () => {
        modal.classList.remove("show");
    });

    // 3. (Opcional pero recomendado) Cierra el modal si haces clic en el fondo oscuro
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.classList.remove("show");
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        modal.classList.add("show");
        
        // Opcional: Limpiar la URL para que no se quede pegado el "?error=" si el usuario cierra el modal
        window.history.replaceState({}, document.title, window.location.pathname);
    }

});