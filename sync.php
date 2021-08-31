<?php	
	$name = $_GET['name'];
	$level = unserialize(file_get_contents('run.db'));
	foreach ($level as $key => $player)
		if ($player[0]<time()-60) {
			unset($level[$key]);
			unlink($key . '.chat');
		}
	unset($level[$name]);
	if ($level!=NULL) echo json_encode($level);
	$level[$name] = array(time(),$_GET['X'],$_GET['Y']);
	file_put_contents('run.db',serialize($level));
?>
