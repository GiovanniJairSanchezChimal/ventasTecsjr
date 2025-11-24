<div class="main-container">

    <form class="box login" action="" method="POST" autocomplete="off" >
    	<p class="has-text-centered">
            <i class="fas fa-user-circle fa-5x"></i>
        </p>
		<h5 class="title is-5 has-text-centered">Inicia sesión con tu cuenta</h5>

		<?php
			if(isset($_POST['login_usuario']) && isset($_POST['login_clave'])){
				$insLogin->iniciarSesionControlador();
			}
		?>

		<div class="field">
			<label class="label"><i class="fas fa-id-card"></i> &nbsp; Número de control / Usuario</label>
			<div class="control">
			    <input class="input" type="text" name="login_usuario" placeholder="Ingresa tu número de control" pattern="[a-zA-Z0-9-]{4,30}" maxlength="30" required >
			</div>
		</div>

		<div class="field">
		  	<label class="label"><i class="fas fa-key"></i> &nbsp; Contraseña</label>
		  	<div class="control">
		    	<input class="input" type="password" name="login_clave" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
		  	</div>
		</div>

		<p class="has-text-centered mb-4 mt-3">
			<button type="submit" class="button is-info is-rounded">LOG IN</button>
		</p>

		<p class="has-text-centered">
			<!-- Enlace apunta directamente al enrutador con query para garantizar carga de vista -->
			<a href="<?php echo APP_URL; ?>index.php?views=clientRegister" class="button is-link is-rounded" id="btn-register">REGISTRARSE</a>
		</p>

	</form>
</div>
