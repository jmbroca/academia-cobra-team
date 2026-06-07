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

document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // 1. MODAL DE INICIAR SESIÓN
    // ==========================================
    const loginModal = document.getElementById("loginModal");
    const btnLogin = document.getElementById("btn-login");
    // Buscamos la 'X' específicamente dentro del modal de login
    const btnCloseLogin = loginModal.querySelector(".close-btn");

    btnLogin.addEventListener("click", () => loginModal.classList.add("show"));
    btnCloseLogin.addEventListener("click", () => loginModal.classList.remove("show"));

    // ==========================================
    // 2. MODAL DE REGISTRO
    // ==========================================
    const registerModal = document.getElementById("registerModal");
    const btnRegister = document.getElementById("btn-register"); // El botón del menú arriba
    const btnCtaRegister = document.getElementById("btn-cta-register"); // EL NUEVO BOTÓN GIGANTE ABAJO
    
    const btnCloseReg = registerModal.querySelector(".close-btn-reg");

    // Ambos botones abren la misma ventana
    btnRegister.addEventListener("click", () => registerModal.classList.add("show"));
    btnCtaRegister.addEventListener("click", () => registerModal.classList.add("show"));
    
    // La X la cierra
    btnCloseReg.addEventListener("click", () => registerModal.classList.remove("show"));

    // ==========================================
    // 3. CERRAR AL HACER CLIC EN EL FONDO OSCURO
    // ==========================================
    window.addEventListener("click", (event) => {
        if (event.target === loginModal) loginModal.classList.remove("show");
        if (event.target === registerModal) registerModal.classList.remove("show");
    });

    // ==========================================
    // 4. ABRIR MODALES AUTOMÁTICAMENTE (POR ERRORES O ÉXITO)
    // ==========================================
    const urlParams = new URLSearchParams(window.location.search);

    // Si viene de un error de login
    if (urlParams.has('error')) {
        loginModal.classList.add("show");
    }

    // Si viene de un registro (ya sea éxito o error)
    if (urlParams.has('error_reg') || urlParams.has('exito')) {
        registerModal.classList.add("show");
    }

    // Limpiar la URL sutilmente para que si el usuario recarga la página manualmente, no le vuelva a salir la ventana
    if (urlParams.has('error') || urlParams.has('error_reg') || urlParams.has('exito')) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});