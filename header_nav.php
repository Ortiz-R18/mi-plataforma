<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        body {
            font-family: Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background-color: black;
            color: white;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
        }
        .sidebar img {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 80%;
        }
        .nav-list {
            list-style-type: none;
            padding: 0;
            margin-top: 80px;
            background-color: black;
        }
        .nav-list > li {
            text-align: left;
            margin: 10px 0;
            padding-left: 20px;
            background-color: black;
            border: 1px solid rgba(255, 255, 255, 0.0);
            padding: 10px; /* Espaciado interior para mejorar la separación */
        }
        .nav-list > li > span {
            font-weight: bold; /* Aplica negrita a los títulos de los menús */
            cursor: pointer; /* Cambia el cursor al pasar sobre los títulos */
        }
        .nav-list li a {
            text-decoration: none;
            color: white;
            background-color: black;
            padding: 10px 0;
            display: block;
            transition: background-color 0.3s;
        }
        .nav-list li a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .sub-menu {
            display: none; /* Ocultar submenús por defecto */
            list-style-type: none;
            padding: 0;
            margin: 0;
            padding-left: 20px; /* Indentación para submenús */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="https://i.ibb.co/tMznSyD/Dise-o-sin-t-tulo.png" alt="Logo">
        <ul class="nav nav-list">
            <li><a href="index.php">INICIO</a></li>
            <li>
                <span onclick="toggleSubMenu(this)">CLIENTES</span>
                <ul class="sub-menu">
                    <li><a href="ver_personas.php">» Consultar Clientes</a></li>
                </ul>
            </li>
            <li>
                <span onclick="toggleSubMenu(this)">CURSOS</span>
                <ul class="sub-menu">
                    <li><a href="cursos.php">» Gestionar cursos</a></li>
                    <li><a href="ver_certificados.php">» Consultar Certificados</a></li>
                </ul>
            </li>
            <li>
                <span onclick="toggleSubMenu(this)">DIPLOMADOS</span>
                <ul class="sub-menu">
                    <li><a href="diplomados.php">» Gestionar Diplomados</a></li>
                    <li><a href="ver_diplomados.php">» Consultar Certificados</a></li>
                </ul>
            <li>
                <span onclick="toggleSubMenu(this)">EMPRESAS</span>
                <ul class="sub-menu">
                    <li><a href="consultar_empresas.php">» Consultar empresas</a></li>
                </ul>    
            </li>
        </ul>
    </div>

    <script>
        function toggleSubMenu(element) {
            // Obtener el submenú asociado al título
            const subMenu = element.nextElementSibling;

            // Alternar la visibilidad del submenú
            if (subMenu.style.display === "block") {
                subMenu.style.display = "none"; // Ocultar si ya está visible
            } else {
                // Ocultar todos los submenús
                const allSubMenus = document.querySelectorAll('.sub-menu');
                allSubMenus.forEach(menu => menu.style.display = "none");

                // Mostrar el submenú actual
                subMenu.style.display = "block";
            }
        }
    </script>
</body>
</html>
