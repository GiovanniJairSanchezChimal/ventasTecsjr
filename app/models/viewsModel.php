<?php
	
	namespace app\models;

	class viewsModel{

		/*---------- Modelo obtener vista ----------*/
		protected function obtenerVistasModelo($vista){

			$listaBlanca=["dashboard","userNew","userList","userUpdate","userSearch","userPhoto","clientNew","clientRegister","clienteRegister","clientDashboard","clientList","clientSearch","clientUpdate","categoryNew","categoryList","categorySearch","categoryUpdate","productNew","productList","productSearch","productUpdate","productPhoto","productCategory","companyNew","saleNew","saleList","saleSearch","saleDetail","logOut"];
			$adminOnly=["userNew","userList","userSearch"];
			$clienteOnlyAllowed=["clientDashboard","clientRegister","logOut"];

			// Alias de vistas para soportar nombres alternativos (ej: 'clienteRegister' -> 'clientRegister')
			$aliasMap=[
				"clienteRegister"=>"clientRegister"
			];

			if(in_array($vista, $listaBlanca)){
				// Si hay sesión de cliente pero también sesión admin, dar prioridad a admin
				if(isset($_SESSION['cliente_id']) && !isset($_SESSION['id'])){
					if(!in_array($vista,$clienteOnlyAllowed)){
						$contenido="404";
						return $contenido;
					}
				}
				// If view is admin-only, allow only session user id 1 (Administrador)
				if(in_array($vista,$adminOnly) && (!isset($_SESSION['id']) || $_SESSION['id']!=1)){
					$contenido="404";
				}else{
					$vistaReal=$vista;
					// Resolver alias si la vista solicitada no existe directamente
					if(!is_file("./app/views/content/".$vista."-view.php") && isset($aliasMap[$vista])){
						$vistaReal=$aliasMap[$vista];
					}
					if(is_file("./app/views/content/".$vistaReal."-view.php")){
						$contenido="./app/views/content/".$vistaReal."-view.php";
					}else{
						$contenido="404";
					}
				}
			}elseif($vista=="login" || $vista=="index"){
				$contenido="login";
			}else{
				$contenido="404";
			}
			return $contenido;
		}

	}