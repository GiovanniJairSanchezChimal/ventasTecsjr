<?php

namespace app\controllers;
use app\models\mainModel;

class pedidoController extends mainModel{

    /* Agregar producto a pedido en sesión del cliente */
    public function agregarProductoPedidoControlador(){
        if(!isset($_SESSION['cliente_id'])){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Sesión no válida",
                "texto"=>"Debe iniciar sesión como cliente para agregar productos",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        $codigo=$this->limpiarCadena($_POST['producto_codigo'] ?? "");
        if($codigo==""){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Código vacío",
                "texto"=>"No se recibió el código del producto",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        if($this->verificarDatos("[a-zA-Z0-9- ]{1,77}",$codigo)){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Formato incorrecto",
                "texto"=>"El código de producto no cumple el formato",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }

        // Se elimina la referencia a producto_stock_total ya que la columna fue removida o puede ser NULL
        $check_producto=$this->ejecutarConsulta("SELECT producto_id, producto_nombre, producto_precio_venta FROM producto WHERE producto_codigo='$codigo'");
        if($check_producto->rowCount()!=1){
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"No encontrado",
                "texto"=>"El producto indicado no existe",
                "icono"=>"error"
            ];
            return json_encode($alerta);
        }
        $producto=$check_producto->fetch();

        // Ya no se valida stock; se permite agregar siempre

        if(!isset($_SESSION['pedido_cliente'])){ $_SESSION['pedido_cliente']=[]; }

        if(!isset($_SESSION['pedido_cliente'][$codigo])){
            $_SESSION['pedido_cliente'][$codigo]=[
                'producto_id'=>$producto['producto_id'],
                'producto_codigo'=>$codigo,
                'producto_nombre'=>$producto['producto_nombre'],
                'cantidad'=>1,
                'precio_unitario'=>$producto['producto_precio_venta'],
                'total_linea'=>$producto['producto_precio_venta']
            ];
        }else{
            $_SESSION['pedido_cliente'][$codigo]['cantidad']++;
            $_SESSION['pedido_cliente'][$codigo]['total_linea'] = number_format($_SESSION['pedido_cliente'][$codigo]['cantidad'] * $_SESSION['pedido_cliente'][$codigo]['precio_unitario'], MONEDA_DECIMALES,'.','');
        }

        $alerta=[
            "tipo"=>"simple",
            "titulo"=>"Producto agregado",
            "texto"=>"Se añadió/actualizó el producto en tu pedido",
            "icono"=>"success"
        ];
        return json_encode($alerta);
    }

    /* Eliminar producto del pedido */
    public function eliminarProductoPedidoControlador(){
        $codigo=$this->limpiarCadena($_POST['producto_codigo'] ?? "");
        if(isset($_SESSION['pedido_cliente'][$codigo])){
            unset($_SESSION['pedido_cliente'][$codigo]);
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"Producto eliminado",
                "texto"=>"Se quitó el producto del pedido",
                "icono"=>"success"
            ];
        }else{
            $alerta=[
                "tipo"=>"simple",
                "titulo"=>"No encontrado",
                "texto"=>"El producto no está en el pedido",
                "icono"=>"error"
            ];
        }
        return json_encode($alerta);
    }

    /* Listar pedido actual (para uso en vista) */
    public function renderPedidoActual(){
        if(!isset($_SESSION['pedido_cliente']) || count($_SESSION['pedido_cliente'])==0){
            return '<p class="has-text-centered">No has agregado productos al pedido aún.</p>';
        }
        $total=0; $items=0; $html='<div class="table-container"><table class="table is-fullwidth is-striped is-narrow"><thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Total</th><th></th></tr></thead><tbody>';
        foreach($_SESSION['pedido_cliente'] as $p){
            $total+= $p['total_linea'];
            $items+= $p['cantidad'];
            $html.='<tr><td>'.htmlspecialchars($p['producto_nombre']).'</td><td>'.$p['cantidad'].'</td><td>$'.number_format($p['precio_unitario'],MONEDA_DECIMALES,'.','').'</td><td>$'.number_format($p['total_linea'],MONEDA_DECIMALES,'.','').'</td><td><button class="button is-danger is-light is-small btn-remove-pedido" data-codigo="'.htmlspecialchars($p['producto_codigo']).'"><i class="far fa-trash-alt"></i></button></td></tr>';
        }
        $html.='</tbody></table></div><p class="has-text-right"><strong>Artículos: '.$items.' | Total pedido: $'.number_format($total,MONEDA_DECIMALES,'.','').'</strong></p>';
        return $html;
    }

    /* Confirmar pedido y registrar venta como Apartado (abono=0) */
    public function confirmarPedidoGenerarVentaControlador(){
        if(!isset($_SESSION['cliente_id'])){
            return json_encode([
                "tipo"=>"simple",
                "titulo"=>"Sesión no válida",
                "texto"=>"Debe iniciar sesión como cliente",
                "icono"=>"error"
            ]);
        }
        if(!isset($_SESSION['pedido_cliente']) || count($_SESSION['pedido_cliente'])==0){
            return json_encode([
                "tipo"=>"simple",
                "titulo"=>"Pedido vacío",
                "texto"=>"Agregue productos antes de confirmar",
                "icono"=>"error"
            ]);
        }

        // Calcular total venta SIN verificar stock ni actualizar existencias
        $venta_total=0; $productosDatos=[];
        foreach($_SESSION['pedido_cliente'] as $codigo=>$p){
            $res=$this->ejecutarConsulta("SELECT producto_id, producto_nombre, producto_precio_compra, producto_precio_venta FROM producto WHERE producto_id='".$p['producto_id']."' AND producto_codigo='".$p['producto_codigo']."'");
            if($res->rowCount()!=1){
                return json_encode([
                    "tipo"=>"simple",
                    "titulo"=>"Producto inválido",
                    "texto"=>"Un producto del pedido ya no existe",
                    "icono"=>"error"
                ]);
            }
            $prod=$res->fetch();
            $linea_total=number_format($p['cantidad']*$prod['producto_precio_venta'],MONEDA_DECIMALES,'.','');
            $venta_total+= $linea_total;
            $productosDatos[$codigo]=[
                'producto_id'=>$prod['producto_id'],
                'producto_codigo'=>$codigo,
                'cantidad'=>$p['cantidad'],
                'precio_compra'=>$prod['producto_precio_compra'],
                'precio_venta'=>$prod['producto_precio_venta'],
                'total_linea'=>$linea_total,
                'descripcion'=>$prod['producto_nombre']
            ];
        }
        $venta_total=number_format($venta_total,MONEDA_DECIMALES,'.','');
        $venta_pagado=number_format(0,MONEDA_DECIMALES,'.','');
        $venta_cambio=number_format(0,MONEDA_DECIMALES,'.','');
        $venta_fecha=date("Y-m-d");
        $venta_hora=date("h:i a");

        // Generar código venta
        $correlativo=$this->ejecutarConsulta("SELECT venta_id FROM venta");
        $correlativo=($correlativo->rowCount())+1;
        $codigo_venta=$this->generarCodigoAleatorio(10,$correlativo);

        // Caja fija (1) y usuario admin (1) por ausencia de cajeros
        $usuario_id=1; $caja_id=1; $cliente_id=$_SESSION['cliente_id'];

        $datos_venta=[
            ["campo_nombre"=>"venta_codigo","campo_marcador"=>":Codigo","campo_valor"=>$codigo_venta],
            ["campo_nombre"=>"venta_fecha","campo_marcador"=>":Fecha","campo_valor"=>$venta_fecha],
            ["campo_nombre"=>"venta_hora","campo_marcador"=>":Hora","campo_valor"=>$venta_hora],
            ["campo_nombre"=>"venta_total","campo_marcador"=>":Total","campo_valor"=>$venta_total],
            ["campo_nombre"=>"venta_pagado","campo_marcador"=>":Pagado","campo_valor"=>$venta_pagado],
            ["campo_nombre"=>"venta_cambio","campo_marcador"=>":Cambio","campo_valor"=>$venta_cambio],
            ["campo_nombre"=>"usuario_id","campo_marcador"=>":Usuario","campo_valor"=>$usuario_id],
            ["campo_nombre"=>"cliente_id","campo_marcador"=>":Cliente","campo_valor"=>$cliente_id],
            ["campo_nombre"=>"caja_id","campo_marcador"=>":Caja","campo_valor"=>$caja_id]
        ];
        $addVenta=$this->guardarDatos("venta",$datos_venta);
        if($addVenta->rowCount()!=1){
            return json_encode([
                "tipo"=>"simple",
                "titulo"=>"Error venta",
                "texto"=>"No se pudo registrar la venta (apartado)",
                "icono"=>"error"
            ]);
        }

        // Insertar detalles
        foreach($productosDatos as $info){
            $datos_det=[
                ["campo_nombre"=>"venta_detalle_cantidad","campo_marcador"=>":Cantidad","campo_valor"=>$info['cantidad']],
                ["campo_nombre"=>"venta_detalle_precio_compra","campo_marcador"=>":PrecioCompra","campo_valor"=>$info['precio_compra']],
                ["campo_nombre"=>"venta_detalle_precio_venta","campo_marcador"=>":PrecioVenta","campo_valor"=>$info['precio_venta']],
                ["campo_nombre"=>"venta_detalle_total","campo_marcador"=>":Total","campo_valor"=>$info['total_linea']],
                ["campo_nombre"=>"venta_detalle_descripcion","campo_marcador"=>":Descripcion","campo_valor"=>$info['descripcion']],
                ["campo_nombre"=>"venta_codigo","campo_marcador"=>":VentaCodigo","campo_valor"=>$codigo_venta],
                ["campo_nombre"=>"producto_id","campo_marcador"=>":Producto","campo_valor"=>$info['producto_id']]
            ];
            $addDet=$this->guardarDatos("venta_detalle",$datos_det);
            if($addDet->rowCount()!=1){
                // rollback detalles y venta (sin gestión de stock)
                $this->eliminarRegistro("venta_detalle","venta_codigo",$codigo_venta);
                $this->eliminarRegistro("venta","venta_codigo",$codigo_venta);
                return json_encode([
                    "tipo"=>"simple",
                    "titulo"=>"Error detalle",
                    "texto"=>"No se pudo registrar un detalle de la venta",
                    "icono"=>"error"
                ]);
            }
        }

        // No afecta caja porque abono inicial = 0
        unset($_SESSION['pedido_cliente']);
        $_SESSION['venta_codigo_factura']=$codigo_venta;
        return json_encode([
            "tipo"=>"simple",
            "titulo"=>"Pedido confirmado",
            "texto"=>"Se registró el pedido. Código: $codigo_venta",
            "icono"=>"success"
        ]);
    }
}
