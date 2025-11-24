<section class="section" style="padding-top:2rem;padding-bottom:2rem;">
	<div class="container">
		<div class="columns is-centered">
			<div class="column is-12-mobile is-8-tablet is-6-desktop is-5-widescreen">
				<form class="box FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/clienteAjax.php" method="POST" autocomplete="off" >
					<p class="has-text-centered mb-3">
						<i class="fas fa-user-plus fa-3x"></i>
					</p>
					<h5 class="title is-5 has-text-centered">Registro de cliente</h5>

          <input type="hidden" name="modulo_cliente" value="registrar">

          <div class="field">
            <label class="label">Tipo de documento <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <div class="select is-fullwidth">
                <select name="cliente_tipo_documento" required>
                  <option value="" selected>Seleccione una opción</option>
                  <?php echo $insLogin->generarSelect(DOCUMENTOS_USUARIOS,"VACIO"); ?>
                </select>
              </div>
            </div>
          </div>

          <div class="field">
            <label class="label">Número de NoControl <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="text" name="cliente_numero_documento" pattern="[a-zA-Z0-9-]{7,30}" maxlength="30" required >
            </div>
          </div>

          <div class="field">
            <label class="label">Nombres <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="text" name="cliente_nombre" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
            </div>
          </div>

          <div class="field">
            <label class="label">Apellidos <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="text" name="cliente_apellido" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}" maxlength="40" required >
            </div>
          </div>

          <div class="field">
            <label class="label">Provincia/Estado <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="text" name="cliente_provincia" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}" maxlength="30" required >
            </div>
          </div>

          <div class="field">
            <label class="label">Ciudad <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="text" name="cliente_ciudad" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{4,30}" maxlength="30" required >
            </div>
          </div>

          <div class="field">
            <label class="label">Dirección <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="text" name="cliente_direccion" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{4,70}" maxlength="70" required >
            </div>
          </div>

          <div class="field">
            <label class="label">Teléfono</label>
            <div class="control">
              <input class="input" type="text" name="cliente_telefono" pattern="[0-9()+]{8,20}" maxlength="20" >
            </div>
          </div>

          <div class="field">
            <label class="label">Email</label>
            <div class="control">
              <input class="input" type="email" name="cliente_email" maxlength="70" >
            </div>
          </div>

          

          <!-- Campos de contraseña -->
          <hr>
          <div class="field">
            <label class="label">Contraseña <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="password" name="cliente_clave_1" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
            </div>
            <p class="help">Mínimo 7 caracteres. Puede usar letras, números y $ @ . -</p>
          </div>

          <div class="field">
            <label class="label">Confirmar contraseña <?php echo CAMPO_OBLIGATORIO; ?></label>
            <div class="control">
              <input class="input" type="password" name="cliente_clave_2" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
            </div>
          </div>
          <p class="has-text-centered mt-4">
            <button type="reset" class="button is-link is-light is-rounded"><i class="fas fa-paint-roller"></i> &nbsp; Limpiar</button>
            <button type="submit" class="button is-info is-rounded"><i class="far fa-save"></i> &nbsp; Guardar</button>
          </p>

					<p class="has-text-centered pt-4"><small>Los campos marcados con <?php echo CAMPO_OBLIGATORIO; ?> son obligatorios</small></p>
					<p class="has-text-centered mt-3">
						<a href="<?php echo APP_URL; ?>login/" class="button is-link is-light is-rounded">Volver al login</a>
					</p>
				</form>
			</div>
		</div>
	</div>
</section>