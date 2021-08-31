<?php
	if ($_GET['name']=='')
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="manifest" href="manifest.json"><form style="margin: 150px 0;"><center><input style="font-size: larger;" name="name" type="text" placeholder="YOUR NAME" /> <input style="font-size: larger;" type="submit" value="PLAY!" /></center></form>'; 
	else {
?>

<html>
<head>
	<title>webDungeon</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<link rel="manifest" href="manifest.json">
	<style type="text/css">
	body {
		overscroll-behavior: contain;
		font-family: sans-serif;	
	}
	#maze {
		border-collapse: collapse;
		margin: 0;
	}
	canvas, #info { 
		display: inline-block;
		vertical-align: middle;
		margin: 0;
	}
	canvas, table {
		border: 3px solid #000000;
	}
	#maze td {
		width: 18px;
		height: 10px;
	}
	h1,h2,table { 
		margin: 0; 
	}
	h3 {
		margin: 24px;
	}
	#message {
		color: red;
	}
	input, #chat { 
		font-size: large; 
		border-radius: 3px; 
		width: 90%; 
		border: 1px solid #555555; 
		background: #cccccc; 
		margin: 10px 0; 
		text-align: center; 
		padding: 3px; 
		color: #000000;
	}
	#chat { 
		height: 200px; 
		overflow-y: scroll;
		text-align: left;
	}
	</style>
</head>
<body>
	<center><div id="head"><h1><span style="color: red;">web</span>Dungeon</h1><h2>by Chris Hrybacz</h2><h3>krzysztof@zygtech.pl</h3><br /><br /></div>
	<canvas id="3d-canvas" width="1280" height="720"></canvas>
	<div id="info"><h3><span id="message"></span><br /><br />KILLS: <span id="kills">0</span></h3><table id="maze">
		<tbody></tbody>
	</table><input id="talk" type="text" disabled placeholder="<?php echo $_GET['name']; ?>" /><div id="chat"></div></div></center>
	<script>
	var playerName = "<?php echo $_GET['name']; ?>";
	</script>
	<script src="webDungeon.js"></script>
</body>
</html>
<?php
	}
?>
