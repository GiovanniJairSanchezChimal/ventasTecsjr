<?php
use app\controllers\productController; 
use app\controllers\pedidoController; 

$insProducto = new productController();
$insPedido = new pedidoController();
?>

<div class="container is-fluid mb-4">
	<div class="columns is-vcentered">
		<div class="column">
			<h1 class="title">Área de Cliente</h1>
			<h2 class="subtitle"><i class="fas fa-shopping-basket"></i> &nbsp; Catálogo de productos</h2>
		</div>
		<div class="column is-narrow has-text-right">
			<a href="<?php echo APP_URL; ?>logOut/" class="button is-danger is-rounded">
				<i class="fas fa-power-off"></i> &nbsp; Cerrar sesión
			</a>
		</div>
	</div>
</div>

<div class="columns">
	<div class="column is-two-thirds">
		<div class="box" id="catalogo-productos">
			<?php echo $insProducto->listarProductoPublicoControlador(); ?>
		</div>
	</div>
	<div class="column">
		<div class="box" id="pedido-actual">
			<h3 class="title is-6">Tu pedido</h3>
			<div id="pedido-lista">
				<?php echo $insPedido->renderPedidoActual(); ?>
			</div>
			<p class="has-text-centered mt-3">
				<button class="button is-link is-rounded" id="btn-finalizar-pedido" disabled>Confirmar pedido (Apartado)</button>
			</p>
			<p class="has-text-centered"><small>Este pedido aún no genera venta ni afecta stock; el administrador registrará el pago.</small></p>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded',()=>{
	const catalogo = document.getElementById('catalogo-productos');
	const pedidoLista = document.getElementById('pedido-lista');
	const btnFinalizar = document.getElementById('btn-finalizar-pedido');

	catalogo.addEventListener('click', async (e)=>{
		const btn = e.target.closest('.btn-add-pedido');
		if(!btn) return;
		const codigo = btn.getAttribute('data-codigo');
		try{
			let formData = new FormData();
			formData.append('modulo_pedido','agregar');
			formData.append('producto_codigo',codigo);
			let res = await fetch('<?php echo APP_URL; ?>app/ajax/pedidoAjax.php',{method:'POST',body:formData});
			let data = await res.json();
			if(data.icono==='success'){
				// Recargar bloque pedido mediante fetch a una pseudo ruta (render vía PHP completo) => simple recarga de página por ahora
				location.reload();
			}else{
				alert(data.texto);
			}
		}catch(err){
			console.error(err);
		}
	});

	pedidoLista.addEventListener('click', async (e)=>{
		const btn = e.target.closest('.btn-remove-pedido');
		if(!btn) return;
		const codigo = btn.getAttribute('data-codigo');
		try{
			let fd = new FormData();
			fd.append('modulo_pedido','eliminar');
			fd.append('producto_codigo',codigo);
			let res = await fetch('<?php echo APP_URL; ?>app/ajax/pedidoAjax.php',{method:'POST',body:fd});
			let data = await res.json();
			location.reload();
		}catch(err){console.error(err);}
	});

	// Habilitar botón finalizar solo si hay productos
	if(pedidoLista.querySelector('table')){ btnFinalizar.disabled=false; }

	btnFinalizar.addEventListener('click', async ()=>{
		if(btnFinalizar.disabled) return;
		btnFinalizar.classList.add('is-loading');
		try{
			let fd=new FormData();
			fd.append('modulo_pedido','confirmar');
			let res=await fetch('<?php echo APP_URL; ?>app/ajax/pedidoAjax.php',{method:'POST',body:fd});
			let data=await res.json();
			btnFinalizar.classList.remove('is-loading');
			alert(data.titulo+"\n"+data.texto);
			if(data.icono==='success'){
				location.reload();
			}
		}catch(err){
			btnFinalizar.classList.remove('is-loading');
			console.error(err);
			alert('Error al confirmar el pedido');
		}
	});
});
</script>
