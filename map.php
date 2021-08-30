<head>
<title>Map Generator</title>
<style>
	body {
		overscroll-behavior: contain;
		font-family: sans-serif;	
	}
	#maze {
		border-collapse: collapse;
		margin: 50px;
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
	h1,h2,h3,table { 
		margin: 0; 
	}
	#message {
		color: red;
	}
	</style>
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="mazegenerator.js"></script>
<script>
	var map;
	function DrawMap2d() {
		$("#maze > tbody td").remove();
		$("#maze > tbody tr").remove();
		for (var i = 0; i < map.length; i++) {
			$("#maze > tbody").append("<tr>");
			for (var j = 0; j < map[i].length; j++) {
				var selector = i+"-"+j;
				$("#maze > tbody").append("<td id='"+selector+"'>&nbsp;</td>");
				if (map[i][j][0] == 0) { $("#"+selector).css("border-top", "2px solid black"); }
				if (map[i][j][1] == 0) { $("#"+selector).css("border-right", "2px solid black"); }
				if (map[i][j][2] == 0) { $("#"+selector).css("border-bottom", "2px solid black"); }
				if (map[i][j][3] == 0) { $("#"+selector).css("border-left", "2px solid black"); }
			}
			$("#maze > tbody").append("</tr>");
		}
	}
	function gen() {
		map = newMaze(16,16);
		DrawMap2d();
	};
	function save() {
		$.post("generate.php", { data: JSON.stringify(map) } );
	}
</script>
</head>
<body>
<center><div id="info"><table id="maze">
		<tbody></tbody>
</table><button onclick="gen()">GENERATE MAP</button> <button onclick="save()">SAVE LEVEL MAP</button></div></center>
<script>
	gen();
</script>
</body>
