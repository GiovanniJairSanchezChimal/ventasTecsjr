<div class="container is-fluid mb-6">
	<h1 class="title">Ventas</h1>
	<h2 class="subtitle"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de Ventas</h2>
</div>
<div class="container pb-6 pt-6">

	<div class="form-rest mb-6 mt-6"></div>

	<?php
		use app\controllers\saleController;

		$insVenta = new saleController();

		echo $insVenta->listarVentaControlador($url[1],15,$url[0],"");

		include "./app/views/inc/print_invoice_script.php";
	?>
</div>

<script>
	function abrirAbonarModal(id, code){
		Swal.fire({
			title: 'Agregar abono',
			html: '<p>Venta: <strong>'+code+'</strong></p><p>Ingrese el monto a abonar</p>',
			input: 'text',
			inputPlaceholder: '0.00',
			showCancelButton: true,
			confirmButtonText: 'Abonar',
			cancelButtonText: 'Cancelar',
			inputValidator: (value) => {
				if(!value || value.trim()===''){
					return 'Debe ingresar un monto';
				}
				let v = value.replace(',','.');
				if(isNaN(parseFloat(v)) || parseFloat(v)<=0){
					return 'Ingrese un monto válido mayor a 0';
				}
			}
		}).then((result) => {
			if(result.isConfirmed){
				let abono = result.value.replace(',','.').trim();
				let datos = new FormData();
				datos.append('venta_id', id);
				datos.append('venta_abono', abono);
				datos.append('modulo_venta', 'abonar_venta');

				fetch('<?php echo APP_URL; ?>app/ajax/ventaAjax.php',{
					method: 'POST',
					body: datos
				})
				.then(respuesta => respuesta.json())
				.then(respuesta => {
					return alertas_ajax(respuesta);
				});
			}
		});
	}
</script>
