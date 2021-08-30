<?php	
	$name = urldecode($_POST['from']);
	$to = urldecode($_POST['to']);
	$message = urldecode($_POST['m']);
	if ($name!='' && $to!='' && $message!='') {
		file_put_contents($to . '.chat',"\n<br />" . $name . ': ' . $message,FILE_APPEND);
		file_put_contents($name . '.chat',"\n<br />" . $name . ': ' . $message,FILE_APPEND);
	}
?>
