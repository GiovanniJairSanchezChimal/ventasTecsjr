<?php
require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\pedidoController;

header("Content-Type: application/json; charset=utf-8");

$insPedido = new pedidoController();

$accion = $_POST['modulo_pedido'] ?? '';

switch($accion){
    case 'agregar':
        echo $insPedido->agregarProductoPedidoControlador();
    break;
    case 'eliminar':
        echo $insPedido->eliminarProductoPedidoControlador();
    break;
    case 'confirmar':
        echo $insPedido->confirmarPedidoGenerarVentaControlador();
    break;
    default:
        echo json_encode([
            'tipo'=>'simple',
            'titulo'=>'Acción inválida',
            'texto'=>'No se reconoció la acción solicitada',
            'icono'=>'error'
        ]);
    break;
}
