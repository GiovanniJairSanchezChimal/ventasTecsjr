<?php

	namespace app\controllers;
	use app\models\mainModel;

	class loginController extends mainModel{

		/*----------  Controlador iniciar sesion  ----------*/
		public function iniciarSesionControlador(){

			$usuario=$this->limpiarCadena($_POST['login_usuario']);
		    $clave=$this->limpiarCadena($_POST['login_clave']);

		    # Verificando campos obligatorios #
		    if($usuario=="" || $clave==""){
				echo '<article class="message is-danger">
				  <div class="message-body">
				    <strong>Ocurrió un error inesperado</strong><br>
				    No has llenado todos los campos que son obligatorios
				  </div>
				</article>';
		    }else{

			    # Verificando integridad de los datos #
			    // Permitimos también guión y mayor longitud para documentos de clientes
				    if($this->verificarDatos("[a-zA-Z0-9-]{4,30}",$usuario)){
					echo '<article class="message is-danger">
					  <div class="message-body">
					    <strong>Ocurrió un error inesperado</strong><br>
					    El USUARIO no coincide con el formato solicitado
					  </div>
					</article>';
			    }else{

			    	# Verificando integridad de los datos #
				    if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
						echo '<article class="message is-danger">
						  <div class="message-body">
						    <strong>Ocurrió un error inesperado</strong><br>
						    La CLAVE no coincide con el formato solicitado
						  </div>
						</article>';
				    }else{

					    # Verificando usuario #
					    $check_usuario=$this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario='$usuario'");

					    if($check_usuario->rowCount()==1){

					    	$check_usuario=$check_usuario->fetch();

					    	if($check_usuario['usuario_usuario']==$usuario && password_verify($clave,$check_usuario['usuario_clave'])){

					    		$_SESSION['id']=$check_usuario['usuario_id'];
					            $_SESSION['nombre']=$check_usuario['usuario_nombre'];
					            $_SESSION['apellido']=$check_usuario['usuario_apellido'];
					            $_SESSION['usuario']=$check_usuario['usuario_usuario'];
					            $_SESSION['foto']=$check_usuario['usuario_foto'];
					            $_SESSION['caja']=$check_usuario['caja_id'];

									// Limpiar cualquier sesión de cliente previa para evitar restricciones de vistas
									unset($_SESSION['cliente_id']);
									unset($_SESSION['cliente_nombre']);
									unset($_SESSION['cliente_apellido']);


								if(headers_sent()){
										echo "<script> window.location.href='".APP_URL."index.php?views=dashboard'; </script>";
								}else{
										header("Location: ".APP_URL."index.php?views=dashboard");
								}

						    }else{
								// Fallback eliminar email como clave; usar documento + contraseña hash
								$check_cliente=$this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_numero_documento='$usuario' LIMIT 1");
								if($check_cliente->rowCount()==1){
									$check_cliente=$check_cliente->fetch();
									if(password_verify($clave,$check_cliente['cliente_clave'])){
										$_SESSION['cliente_id']=$check_cliente['cliente_id'];
										$_SESSION['cliente_nombre']=$check_cliente['cliente_nombre'];
										$_SESSION['cliente_apellido']=$check_cliente['cliente_apellido'];
										if(headers_sent()){
											echo "<script> window.location.href='".APP_URL."clientDashboard/'; </script>";
										}else{
											header("Location: ".APP_URL."clientDashboard/");
										}
									}else{
										echo '<article class="message is-danger">\n  <div class="message-body">\n    <strong>Ocurrió un error inesperado</strong><br>\n    Documento o contraseña incorrectos\n  </div>\n</article>';
									}
								}else{
									echo '<article class="message is-danger">\n  <div class="message-body">\n    <strong>Ocurrió un error inesperado</strong><br>\n    Usuario o clave incorrectos\n  </div>\n</article>';
								}
						    }

					    }else{
						    // Intentar como cliente (documento)
							$check_cliente=$this->ejecutarConsulta("SELECT * FROM cliente WHERE cliente_numero_documento='$usuario' LIMIT 1");
							if($check_cliente->rowCount()==1){
								$check_cliente=$check_cliente->fetch();
								if(password_verify($clave,$check_cliente['cliente_clave'])){
									$_SESSION['cliente_id']=$check_cliente['cliente_id'];
									$_SESSION['cliente_nombre']=$check_cliente['cliente_nombre'];
									$_SESSION['cliente_apellido']=$check_cliente['cliente_apellido'];
									if(headers_sent()){
										echo "<script> window.location.href='".APP_URL."clientDashboard/'; </script>";
									}else{
										header("Location: ".APP_URL."clientDashboard/");
									}
								}else{
									echo '<article class="message is-danger">\n  <div class="message-body">\n    <strong>Ocurrió un error inesperado</strong><br>\n    Documento o contraseña incorrectos\n  </div>\n</article>';
								}
							}else{
								echo '<article class="message is-danger">\n  <div class="message-body">\n    <strong>Ocurrió un error inesperado</strong><br>\n    Usuario o clave incorrectos\n  </div>\n</article>';
							}
					    }
				    }
			    }
		    }
		}


		/*----------  Controlador cerrar sesion  ----------*/
		public function cerrarSesionControlador(){

			session_destroy();

		    if(headers_sent()){
                echo "<script> window.location.href='".APP_URL."login/'; </script>";
            }else{
                header("Location: ".APP_URL."login/");
            }
		}

	}