<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobra Team - Academia de Artes Marciales</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body id="inicio">

    <header class="navbar">
        <div class="logo">
            <div class="logo-icon-box"><i class='bx bx-trophy'></i></div>
            <span class="logo-text">COBRA TEAM</span>
        </div>
        
        <nav class="nav-links">
            <a href="#inicio">Inicio</a>
            <a href="#nosotros">Nosotros</a>
            <a href="#disciplinas">Disciplinas</a>
            <a href="#contacto">Contacto</a>
        </nav>

        <div class="nav-buttons">
            <button id="btn-login" class="btn btn-red">Iniciar Sesión</button>
            <button class="btn btn-outline">Registrarse</button>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>DESATA TU <span class="text-red">PODER<br>INTERIOR</span></h1>
            <p>Únete a la élite de las artes marciales. Transforma tu cuerpo, mente y espíritu.</p>
            <button class="btn btn-red btn-large">COMENZAR AHORA</button>
        </div>
    </section>
    
    <section class="features-section">
        <h2 class="section-title">¿POR QUÉ <span class="text-red">COBRA TEAM</span>?</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class='bx bx-trophy'></i></div> 
                <h3>Instructores Certificados</h3>
                <p>Entrenadores con más de 15 años de experiencia y certificaciones internacionales.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class='bx bx-group'></i></div>
                <h3>Comunidad Fuerte</h3>
                <p>Únete a una familia de más de 500 estudiantes comprometidos con la excelencia.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon"><i class='bx bx-medal'></i></div>
                <h3>Resultados Garantizados</h3>
                <p>Programas diseñados para llevarte del nivel principiante al competitivo.</p>
            </div>
        </div>
    </section>

    <section id="nosotros" class="about-wrapper">
            <div class="about-section">
                <div class="about-content">
                    <h2 class="section-title text-left">SOBRE <span class="text-red">NOSOTROS</span></h2>
                    <p>Cobra Team es más que un dojo, es un estilo de vida. Fundado en 2010, hemos formado campeones nacionales e internacionales en diversas disciplinas de artes marciales.</p>
                    <p>Nuestro enfoque combina técnicas tradicionales con metodologías modernas de entrenamiento, garantizando el desarrollo integral de cada alumno.</p>
                    
                    <div class="about-stats">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Alumnos</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">15</span>
                            <span class="stat-label">Años</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Campeonatos</span>
                        </div>
                    </div>
                </div>
                
                <div class="about-image">
                    <img src="assets/img/sobre-nosotros.webp" alt="Entrenamiento en Cobra Team">
                </div>
            </div>
        </section>

        <section id="disciplinas" class="features-section">
            <h2 class="section-title">NUESTRAS <span class="text-red">DISCIPLINAS</span></h2>
            
            <div class="disciplines-grid">
                <div class="discipline-card">
                    <img src="assets/img/karate.jpg" alt="Karate">
                    <div class="discipline-overlay">
                        <h3>Karate</h3>
                    </div>
                </div>
                
                <div class="discipline-card">
                    <img src="assets/img/taekwondo.webp" alt="Taekwondo">
                    <div class="discipline-overlay">
                        <h3>Taekwondo</h3>
                    </div>
                </div>
                
                <div class="discipline-card">
                    <img src="assets/img/kickboxing.webp" alt="Kickboxing">
                    <div class="discipline-overlay">
                        <h3>Kickboxing</h3>
                    </div>
                </div>
                
                <div class="discipline-card">
                    <img src="assets/img/jiujitsu.jpg" alt="Jiu-Jitsu">
                    <div class="discipline-overlay">
                        <h3>Jiu-Jitsu</h3>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-content">
                <h2>¿LISTO PARA EL DESAFÍO?</h2>
                <p>No esperes más. Tu transformación comienza hoy.</p>
                <button class="btn btn-white btn-large">REGISTRARME AHORA</button>
            </div>
        </section>

    <footer id="contacto" class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <div class="footer-logo">
                    <div class="logo-icon-box"><i class='bx bx-trophy'></i></div>
                    <span class="logo-text">COBRA TEAM</span>
                </div>
                <p class="footer-desc">Formando campeones desde 2010</p>
            </div>

            <div class="footer-col">
                <h4>Enlaces</h4>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Nosotros</a></li>
                    <li><a href="#">Clases</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Horarios</h4>
                <ul>
                    <li>Lunes - Viernes: 6am - 9pm</li>
                    <li>Sábados: 8am - 6pm</li>
                    <li>Domingos: 9am - 2pm</li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <ul>
                    <li>info@cobrateam.com</li>
                    <li>+52 123 456 7890</li>
                    <li>Av. Principal #123, CDMX</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 Cobra Team. Todos los derechos reservados.</p>
        </div>
    </footer>

    <div id="loginModal" class="modal">
        <div class="modal-content">
            <i class='bx bx-x close-btn'></i>
            
            <h2>INICIAR SESIÓN</h2>
            
            <form action="#" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="tu@email.com" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn btn-red btn-full">ENTRAR</button>
            </form>
        </div>
    </div>

    <script src="assets/js/main.js"></script>

</body>

</html>