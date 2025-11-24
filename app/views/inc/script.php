<?php
	$ajax_path = __DIR__ . "/../js/ajax.js";
	$main_path = __DIR__ . "/../js/main.js";
	$ajax_v = file_exists($ajax_path) ? filemtime($ajax_path) : time();
	$main_v = file_exists($main_path) ? filemtime($main_path) : time();
?>
<script src="<?php echo APP_URL; ?>app/views/js/ajax.js?v=<?php echo $ajax_v; ?>" ></script>
<script src="<?php echo APP_URL; ?>app/views/js/main.js?v=<?php echo $main_v; ?>" ></script>