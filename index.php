<?php

    require_once "./config/app.php";
    require_once "./autoload.php";

    /*---------- Iniciando sesion ----------*/
    require_once "./app/views/inc/session_start.php";

    if(isset($_GET['views'])){
        $url=explode("/", $_GET['views']);
    }else{
        $url=["login"];
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once "./app/views/inc/head.php"; ?>
</head>
<body>
    <?php
        use app\controllers\viewsController;
        use app\controllers\loginController;

        $insLogin = new loginController();

        $viewsController= new viewsController();
        $vista=$viewsController->obtenerVistasControlador($url[0]);

        // Determinar slug solicitado original para comparar, evitando usar la ruta completa devuelta en $vista
        $slug = $url[0];
        // Vistas públicas sin sesión: login, registro cliente
        $publicas = ["login","clientRegister","clienteRegister"];
        // Detectar si la vista calculada es un archivo de contenido
        $esArchivoContenido = (strpos($vista,"./app/views/content/") === 0);
        // Si slug es pública o es 404 o cliente autenticado accediendo a vistas que empiezan con client/cliente
        if(in_array($slug,$publicas) || $vista=="404" || (isset($_SESSION['cliente_id']) && ($slug!="" && (strpos($slug,'client')===0 || strpos($slug,'cliente')===0)))){
            // Para login y 404 el modelo nos devuelve 'login' o '404'; para otras devuelve ruta completa
            if($vista=="login"){
                require_once "./app/views/content/login-view.php";
            }elseif($vista=="404"){
                require_once "./app/views/content/404-view.php";
            }else{
                // Si $vista es ruta completa úsala directamente, si es solo slug construir ruta
                if($esArchivoContenido){
                    require_once $vista;
                }else{
                    require_once "./app/views/content/".$slug."-view.php";
                }
            }
        }else{
    ?>
    <main class="page-container">
    <?php
            # Cerrar sesion #
            if((!isset($_SESSION['id']) || $_SESSION['id']=="") || (!isset($_SESSION['usuario']) || $_SESSION['usuario']=="")){
                $insLogin->cerrarSesionControlador();
                exit();
            }
            require_once "./app/views/inc/navlateral.php";
    ?>      
        <section class="full-width pageContent scroll" id="pageContent">
            <?php
                require_once "./app/views/inc/navbar.php";

                require_once $vista;

            ?>
        </section>
    </main>
    <?php
        }

        require_once "./app/views/inc/script.php"; 
    ?>
</body>
</html>