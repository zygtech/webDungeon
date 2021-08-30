<?php
	if ($_GET['name']=='')
		echo '<form><center><input name="name" type="text" placeholder="YOUR NAME" /> <input type="submit" value="PLAY!" /></center></form>'; 
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
	</table><input id="talk" type="text" disabled placeholder="TALK" /><div id="chat"></div></div></center>
	<script>
		var canvas = document.getElementById("3d-canvas");
	var ctx = canvas.getContext("2d");
	var map;
	var walls = [];
	var collide = [];
	var isWalk = false;
	var isBackwards = false;
	var pmouseX = 0;
	var pmouseY = 0;
	var cameraAngle = 0.8;
	var cameraVertical = 0;
	var CollisionDistance = 100;
	var DrawDistance = 0.015;
	var ParseDistance = 7;
	var DepthAttribute = 0.002;
	var FPS = 30;
	var space = 1200;
	var size = 16;
	var cameraX = space/2;
	var cameraY = space/2;
	var clock;
	var attack = false;
	var hit = 0;
	var kills = 0;
	var updated = [];
	var players = [];
	var playern = "";
	var enemies = [];
	var minEnemies = 16;
	var maxEnemies = 32;
	var cycle = 0;
	var seconds = 0;
	var tap = 0;
	var IMGsword1=new Image();
	var IMGsword2=new Image();
	var IMGenemy=new Image();
	var IMGplayer=new Image();
	IMGsword1.src="sword1.png";
	IMGsword2.src="sword2.png";
	IMGenemy.src="enemy.png";
	IMGplayer.src="player.png";
	
	function tick() {
		clock = setTimeout(function(){ tick() }, 1000/FPS);
		cycle++;
		if (tap>0) { tap--; }
		if (cycle%FPS==0) { cycle=0; seconds++; }
		if (seconds%60==0) { seconds = 0; }
		if (seconds%3==0) {
			readChat();
			updatePlayers(); 
			for (var i = 0; i < enemies.length; i++) {
				if (enemies[i][1]>0 && Math.floor(Math.random()*4)==0 && enemies[i][4][0]==0 && enemies[i][4][1]==0) {
					var move = true;
					while (move) {
						var direction = Math.floor(Math.random()*4);
						if (Math.floor(cameraX/space)==enemies[i][0][0]-1 && Math.floor(cameraY/space)==enemies[i][0][1] && map[enemies[i][0][0]][enemies[i][0][1]][0]!=0) { direction=0; }
						if (Math.floor(cameraX/space)==enemies[i][0][0] && Math.floor(cameraY/space)==enemies[i][0][1]+1 && map[enemies[i][0][0]][enemies[i][0][1]][1]!=0) { direction=1; }
						if (Math.floor(cameraX/space)==enemies[i][0][0]+1 && Math.floor(cameraY/space)==enemies[i][0][1] && map[enemies[i][0][0]][enemies[i][0][1]][2]!=0) { direction=2; }
						if (Math.floor(cameraX/space)==enemies[i][0][0] && Math.floor(cameraY/space)==enemies[i][0][1]-1 && map[enemies[i][0][0]][enemies[i][0][1]][3]!=0) { direction=3; }		
						if (map[enemies[i][0][0]][enemies[i][0][1]][direction]!=0) {
							switch (direction) {
								case 0:
									if (enemies[i][0][0]>0) { enemies[i][4][0]=-1; move = false; }
									break;
								case 1:
									if (enemies[i][0][1]<size-1) { enemies[i][4][1]=1; move = false; }
									break;
								case 2:
									if (enemies[i][0][0]<size-1) { enemies[i][4][0]=1; move = false; }
									break;
								case 3:
									if (enemies[i][0][1]>0) { enemies[i][4][1]=-1; move = false; }
									break;
							}
							for (var n = 0; n < enemies.length; n++) {
								if ((enemies[n][0][0]==enemies[i][0][0]+enemies[i][4][0] && enemies[n][0][1]==enemies[i][0][1]+enemies[i][4][1]) ||
									(enemies[n][0][0]+enemies[n][4][0]==enemies[i][0][0]+enemies[i][4][0] && enemies[n][0][1]+enemies[n][4][1]==enemies[i][0][1]+enemies[i][4][1] && n!=i)) {
									enemies[i][4][0]=0;
									enemies[i][4][1]=0;
								}
							}
						}
					}
				}
			}
		}
		if (hit==3) {
			for (var i = 0; i < enemies.length; i++) {
				if (enemies[i][1]>0 && enemies[i][2]!=null) {
					if (enemies[i][2][2]>0 && enemies[i][2][2]<400 && enemies[i][2][0]>-200 && enemies[i][2][0]<200) {
						enemies[i][1]=enemies[i][1]-hit;
						if (enemies[i][1]<=0) { enemies[i][1]=0; kills++; document.getElementById("kills").innerHTML=kills; }
					}
				}
			}		
		} else {
			for (var i = 0; i < enemies.length; i++) {
				if (enemies[i][1]>0 && enemies[i][2]!=null) {
					if (enemies[i][2][2]>0 && enemies[i][2][2]<100 && enemies[i][2][0]>-200 && enemies[i][2][0]<200) {
						clearTimeout(clock); document.getElementById("message").innerHTML="GAME OVER";
					}
				}
			}
		}
		hit=0;
		if (isWalk) {
			if (isBackwards) { speed = -7; } else { speed = 16; };
			var NcameraX = cameraX + speed * Math.sin(cameraAngle);
			var NcameraY = cameraY + speed * Math.cos(cameraAngle);

			var check = collide[0];
			if (isBackwards) { check = -collide[1]; }
			
			if (check>CollisionDistance) {
				cameraX = NcameraX;
				cameraY = NcameraY;			
			} else { isWalk = false; isBackwards = false; }
		}
		world3d();
	}
	
	function checkLock() {
		if(document.pointerLockElement === canvas || document.mozPointerLockElement === canvas) {
			return true;
		} else {
			return false;
		}
	}
	
	function readChat() {
		$("#chat").load("read.php", { name: "<?php echo $_GET['name']; ?>" });
	}

	function onmousedown(e) { if (checkLock()) { isWalk = true; if (e.button===2) { isBackwards = true; } else { isBackwards = false; }; } };
	function onmouseup(e) { isWalk = false; isBackwards = false; };
	function onmousemove(e) { if (checkLock()) { var mX = e.movementY/300; var mY = e.movementX/300; cameraAngle+=mY; if ((cameraVertical+mX)>-0.25 && (cameraVertical+mX)<0.25) { cameraVertical+=mX; }; }; };
	function oncontextmenu(e) { e.preventDefault(); e.stopPropagation(); };
	function ontouchstart(e) { if (tap>0) { attack=true; hit=3; }; tap=10; pmouseX = e.changedTouches[0].pageX; pmouseY = e.changedTouches[0].pageY; isWalk = true; if (e.changedTouches[0].pageY>canvas.height*2/3) { isBackwards = true; }; }
	function ontouchend(e) { isWalk = false; isBackwards = false; attack=false; hit=-1; }
	function ontouchmove(e) { var mX = (e.changedTouches[0].pageY-pmouseY)/5000; var mY = (e.changedTouches[0].pageX-pmouseX)/5000; cameraAngle+=mY; if ((cameraVertical+mX)>-0.25 && (cameraVertical+mX)<0.25) { cameraVertical+=mX; }; }
	function onclick(e) { canvas.requestPointerLock(); }
	function onkeydown(e) { 
		talk = document.getElementById("talk");
		if (e.key == 'Shift') { 
			e.preventDefault(); 
			if (attack==false) { 
				attack=true; 
				hit=3;
			};
		} else if (event.keyCode === 13) {
				$.post("chat.php", { from: "<?php echo $_GET['name']; ?>", to: playern, m: talk.value });
				talk.value="";
			}
			else if (e.key == 'Backspace') talk.value=talk.value.substr(0, talk.value.length - 1); 
				else if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 65 && event.keyCode <= 90)) talk.value += e.key; }
	function onkeyup(e) { if (e.key == 'Shift') { e.preventDefault(); attack=false; }; }

	canvas.addEventListener("mousedown", onmousedown, false);
	canvas.addEventListener("mouseup", onmouseup, false);
	canvas.addEventListener("mousemove", onmousemove, false);
	canvas.addEventListener("contextmenu", oncontextmenu, false);
	canvas.addEventListener("click", onclick, false);
	canvas.addEventListener("touchstart", ontouchstart, false);
	canvas.addEventListener("touchend", ontouchend, false);
	canvas.addEventListener("touchmove", ontouchmove, false);
	document.addEventListener("keydown", onkeydown, false);
	document.addEventListener("keyup", onkeyup, false);
	
	var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
	if (isMobile) {
		canvas.width = document.documentElement.clientWidth-20;
		canvas.height = (10 * canvas.width / 16)-25;
		$("#head").hide();
		$("#info").hide();
		if (document.documentElement.clientHeight-canvas.height>123) {
			$("#head").show();
		}
	}
	
	function calculateIntersection(p1, p2, p3, p4) {
		var d1 = (p1[0] - p2[0]) * (p3[1] - p4[1]);
		var d2 = (p1[1] - p2[1]) * (p3[0] - p4[0]);
		var d  = (d1) - (d2);

		if(d == 0) {
			return { x: 0, y: 0 };
		}

		var u1 = (p1[0] * p2[1] - p1[1] * p2[0]);
		var u4 = (p3[0] * p4[1] - p3[1] * p4[0]);

		var u2x = p3[0] - p4[0];
		var u3x = p1[0] - p2[0];
		var u2y = p3[1] - p4[1];
		var u3y = p1[1] - p2[1];

		var px = (u1 * u2x - u3x * u4) / d;
		var py = (u1 * u2y - u3y * u4) / d;

		var p = { x: px, y: py };

		return p;
	}
	
	function wall3d(orderZ,nodes = null,object = null) {
		if (object!=null) {
			if (object[2][2]>0) {
				var color = 255 - orderZ*DrawDistance;
				if (color > 127) {
					if (object[5]==null)
						ctx.drawImage(IMGenemy,((object[2][0]-64)/(object[2][2]*DepthAttribute)+canvas.width/2),((object[2][1]-128)/(object[2][2]*DepthAttribute)+canvas.height/2),128/(object[2][2]*DepthAttribute),256/(object[2][2]*DepthAttribute));
					else {
						ctx.drawImage(IMGplayer,((object[2][0]-64)/(object[2][2]*DepthAttribute)+canvas.width/2),((object[2][1]-128)/(object[2][2]*DepthAttribute)+canvas.height/2),128/(object[2][2]*DepthAttribute),256/(object[2][2]*DepthAttribute));
						if (playern=="") {
							document.getElementById("message").innerHTML="YOU SEE: " + object[5];
							playern=object[5];
						}
					}
				}
			}
		} else if (nodes!=null) {
			ctx.beginPath();
			node = nodes[3];
		
			var x = node[0];
			var y = node[1];
			var z = node[2];
			if (z<=0) { z=1/Math.abs(z); }
		
			var x3d = x/(z*DepthAttribute);
			var y3d = y/(z*DepthAttribute);	
			
			ctx.moveTo(x3d+canvas.width/2,y3d+canvas.height/2);
		
			for (var n = 0; n < 4; n++) {
				node = nodes[n];
		
				var x = node[0];
				var y = node[1];
				var z = node[2];
				if (z<=0) { z=1/Math.abs(z); }
		
				var x3d = x/(z*DepthAttribute);
				var y3d = y/(z*DepthAttribute);
			
				ctx.lineTo(x3d+canvas.width/2,y3d+canvas.height/2);			
			}
		
			var color = 255 - orderZ*DrawDistance;
			if (color<127) { color = 127; }
			ctx.fillStyle = "rgb("+color+","+color+","+color+")";
			ctx.fill();
			var color = 255 - color;
			ctx.strokeStyle = "rgb("+color+","+color+","+color+")";
			ctx.stroke();
		
		}
	}
	
	function parseMap() {
		walls = [];
		$("#maze td").css("background", "none");
		$("#maze td#" + (size-1) + "-" + (size-1)).css("background", "red");
		var tX = Math.floor(cameraX/space);
		var tY =  Math.floor(cameraY/space);
		$("#maze td#" + tX + "-" + tY).css("background", "red");
		if (tX==size-1 && tY==size-1) { clearTimeout(clock); document.getElementById("message").innerHTML="CONGRATULATIONS"; }
		var startX = tX-ParseDistance;
		var startY = tY-ParseDistance;
		var endX = startX+ParseDistance*2;
		var endY = startY+ParseDistance*2;
		if (startX<0) { startX=0; }
		if (startY<0) { startY=0; }
		if (endX>size) { endX=size; }
		if (endY>size) { endY=size; }
		for (var y = startY; y < endY; y++) {
			for (var x = startX; x < endX; x++) {
				if (y==0) {
					walls.push([[x,0],[x+1,0]]);
				}
				if (y==size-1) {
					walls.push([[x,size],[x+1,size]]);
				}
				if (map[x][y][2]==0) {
					walls.push([[x+1,y],[x+1,y+1]]);
				}	
				if (x==0) {
					walls.push([[0,y],[0,y+1]]);
				}
				if (x==size-1) {
					walls.push([[size,y],[size,y+1]]);
				} 
				if (map[x][y][1]==0) {
					walls.push([[x,y+1],[x+1,y+1]]);
				}			
			}
		}
	}
	
	function world3d() {
		
		playern="";
		var draw = false;
		ctx.fillStyle = "rgb(127,127,127)";
		ctx.fillRect(0,0,canvas.width,canvas.height);
		
		var cX = Math.round(cameraX);
		var cY = Math.round(cameraY);
		
		var sortWalls = [];
		
		parseMap();
		
		minimumY = 500;
		maximumY = -500;
		
		for (var i = 0; i < walls.length; i++) {
			var wall = walls[i];
			var coord1 = wall[0];
			var coord2 = wall[1];
			cameraAngle = cameraAngle%360;
			var node0 = [ coord1[0]*space - cX, -canvas.height/2, coord1[1]*space - cY ];
			var node1 = [ coord1[0]*space - cX,  canvas.height/2, coord1[1]*space - cY ];
			var node2 = [ coord2[0]*space - cX,  canvas.height/2, coord2[1]*space - cY ];
			var node3 = [ coord2[0]*space - cX, -canvas.height/2, coord2[1]*space - cY ];
			var nodes = [ node0, node1, node2, node3 ];
			
			var sinTheta = Math.sin(cameraAngle);
			var cosTheta = Math.cos(cameraAngle);
			for (var n = 0; n < 4; n++) {
				var node = nodes[n];
				var x = node[0];
				var z = node[2];
				node[0] = x * cosTheta - z * sinTheta;
				node[2] = z * cosTheta + x * sinTheta;
				nodes[n] = node;
			}	
			
			var M1 = Math.sqrt(Math.pow(nodes[1][0],2)+Math.pow(nodes[1][2],2));
			var M2 = Math.sqrt(Math.pow(nodes[2][0],2)+Math.pow(nodes[2][2],2));
			
			if ((nodes[1][2]>0 || nodes[2][2]>0)) { 
				
				draw = true; 
			
				var point = calculateIntersection([nodes[1][0],nodes[1][2]],[nodes[2][0],nodes[2][2]],[0,0],[0,1]);
			
				if (point.y<0) { 
					if (maximumY<point.y && (point.y<nodes[1][2] || point.y<nodes[2][2]) && ((nodes[1][0]<0 && nodes[2][0]>0) || (nodes[1][0]>0 && nodes[2][0]<0))) { maximumY = point.y; }
					if (nodes[1][2]<0 && nodes[2][2]>0 && nodes[2][0]>0) {
						nodes[1][0] = Math.abs(nodes[1][0]);	
						nodes[0][0] = Math.abs(nodes[0][0]);			
					} else if (nodes[1][2]<0 && nodes[2][2]>0 && nodes[2][0]<0) {
						nodes[1][0] = -Math.abs(nodes[1][0]);
						nodes[0][0] = -Math.abs(nodes[0][0]);	
					} else if (nodes[2][2]<0 && nodes[1][2]>0 && nodes[1][0]>0) {
						nodes[2][0] = Math.abs(nodes[2][0]);	
						nodes[3][0] = Math.abs(nodes[3][0]);
					} else if (nodes[2][2]<0 && nodes[1][2]>0 && nodes[1][0]<0) {
						nodes[2][0] = -Math.abs(nodes[2][0]);
						nodes[3][0] = -Math.abs(nodes[3][0]);	
					} else if (nodes[1][2]<0 && nodes[2][2]<0) { draw = false; }
					var x1 = nodes[1][0]/(nodes[1][2]*DepthAttribute);
					var x2 = nodes[2][0]/(nodes[2][2]*DepthAttribute);
					if (Math.abs(x1)>canvas.width/2 && Math.abs(x2)>canvas.width/2) { draw = false; }
				} else if (minimumY>point.y && (point.y>nodes[1][2] || point.y>nodes[2][2]) && ((nodes[1][0]<0 && nodes[2][0]>0) || (nodes[1][0]>0 && nodes[2][0]<0))) { minimumY = point.y; }
						
				var M = (M2+M1)/2;					
					
				var sinTheta = Math.sin(cameraVertical);
				var cosTheta = Math.cos(cameraVertical);
				for (var n = 0; n < 4; n++) {
					var node = nodes[n];
					var y = node[1];
					var z = node[2];
					node[1] = y * cosTheta - z * sinTheta;
					node[2] = z * cosTheta + y * sinTheta;
					nodes[n] = node;
				}
			
				var color = 255 - M*DrawDistance;
			
				if (draw && color>127) { 
					sortWalls.push([M, nodes, null]);
				}
			}
			
		}
		var tX = Math.floor(cameraX/space);
		var tY =  Math.floor(cameraY/space);
		var startX = tX-ParseDistance*2;
		var startY = tY-ParseDistance*2;
		var endX = startX+ParseDistance*4;
		var endY = startY+ParseDistance*4;
		if (startX<0) { startX=0; }
		if (startY<0) { startY=0; }
		if (endX>size) { endX=size; }
		if (endY>size) { endY=size; }
		
		for (var i = 0; i < players.length; i++) {
				if ((players[i][4][0] > 0 && players[i][0][0] > players[i][3][0]) || (players[i][4][0] < 0 && players[i][0][0] < players[i][3][0])) players[i][4][0]=0;
				if ((players[i][4][1] > 0 && players[i][0][1] > players[i][3][1]) || (players[i][4][1] < 0 && players[i][0][1] < players[i][3][1])) players[i][4][1]=0;
				players[i][0]=[ Math.round(players[i][0][0]+players[i][4][0]), Math.round(players[i][0][1]+players[i][4][1]) ];
				var Pnode = [ players[i][0][0] - cX, 0, players[i][0][1] - cY ];
				var sinTheta = Math.sin(cameraAngle);
				var cosTheta = Math.cos(cameraAngle);
				var x = Pnode[0];
				var z = Pnode[2];
				Pnode[0] = x * cosTheta - z * sinTheta;
				Pnode[2] = z * cosTheta + x * sinTheta;
				var sinTheta = Math.sin(cameraVertical);
				var cosTheta = Math.cos(cameraVertical);
				var y = Pnode[1];
				var z = Pnode[2];
				Pnode[1] = y * cosTheta - z * sinTheta;
				Pnode[2] = z * cosTheta + y * sinTheta;
				var M = Math.sqrt(Math.pow(Pnode[0],2)+Math.pow(Pnode[2],2));
				players[i][2] = Pnode;
				console.log(players[i]);
				sortWalls.push([M,null,players[i]]);
		}
		
		for (var i = 0; i < enemies.length; i++) {
			if (enemies[i][4][0]!=0) {
				enemies[i][3][0]+=enemies[i][4][0]*12;
				if (enemies[i][3][0]>=space*3/2) {
					enemies[i][0][0]++;
					enemies[i][3][0]=space/2;
					enemies[i][4][0]=0;
				}
				if (enemies[i][3][0]<=-space/2) {
					enemies[i][0][0]--;
					enemies[i][3][0]=space/2;
					enemies[i][4][0]=0;
				}
			}
			if (enemies[i][4][1]!=0) {
				enemies[i][3][1]+=enemies[i][4][1]*12;
				if (enemies[i][3][1]>=space*3/2) {
					enemies[i][0][1]++;
					enemies[i][3][1]=space/2;
					enemies[i][4][1]=0;
				}
				if (enemies[i][3][1]<=-space/2) {
					enemies[i][0][1]--;
					enemies[i][3][1]=space/2;
					enemies[i][4][1]=0;
				}
			}
			if (enemies[i][0][0]>=startX && enemies[i][0][0]<endX && enemies[i][0][1]>=startY && enemies[i][0][1]<endY && enemies[i][1]>0) {
				var Enode = [ enemies[i][0][0]*space - cX + enemies[i][3][0], 0, enemies[i][0][1]*space - cY + enemies[i][3][1] ];
				var sinTheta = Math.sin(cameraAngle);
				var cosTheta = Math.cos(cameraAngle);
				var x = Enode[0];
				var z = Enode[2];
				Enode[0] = x * cosTheta - z * sinTheta;
				Enode[2] = z * cosTheta + x * sinTheta;
				var sinTheta = Math.sin(cameraVertical);
				var cosTheta = Math.cos(cameraVertical);
				var y = Enode[1];
				var z = Enode[2];
				Enode[1] = y * cosTheta - z * sinTheta;
				Enode[2] = z * cosTheta + y * sinTheta;
				var M = Math.sqrt(Math.pow(Enode[0],2)+Math.pow(Enode[2],2));
				enemies[i][2] = Enode;
				sortWalls.push([M,null,enemies[i]]);
			} else { enemies[i][2] = null; }
		}		
		
		sortWalls.sort(function(a, b) { return b[0]-a[0]; });
		for (var i = 0; i < sortWalls.length; i++) { wall3d(sortWalls[i][0],sortWalls[i][1],sortWalls[i][2]); }
		if (playern=="") document.getElementById("message").innerHTML="";
		if (attack) { ctx.drawImage(IMGsword2,0,0,canvas.width,canvas.height); }
			else { ctx.drawImage(IMGsword1,0,0,canvas.width,canvas.height); }
		collide = [ minimumY, maximumY ];
	}
	
	function updatePlayers() {
		var coords = [];
		
		$.get("sync.php?name=<?php echo $_GET['name']; ?>&X=" + cameraX + "&Y=" + cameraY, function(response) {
			if (response!="") 
				coords=JSON.parse(response);
			else
				coords = null;
			updated = [];
			if (coords!=null) Object.keys(coords).forEach(function(key) {
				var xcoord,ycoord;
				xcoord = coords[key][1];
				ycoord = coords[key][2];
				for (var i = 0; i < players.length; i++) 
					if (players[i][5]=key) {
						xcoord=players[i][0][0];
						ycoord=players[i][0][1];
					}
				updated.push([ [ xcoord, ycoord ], 3, null, [ coords[key][1], coords[key][2] ], [ ((coords[key][1]-xcoord)/10), ((coords[key][2]-ycoord)/10) ], key ]);		
			});
		});
		players=updated;
	}
	
	function DrawMap2d() {
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
	
	function init() {
		$.get("map.db", function(response) {
			map = JSON.parse(response);
			DrawMap2d();
		});
		for (var i = 0; i < Math.floor(Math.random()*(maxEnemies-minEnemies))+minEnemies; i++) {
			var enemyX=0;
			var enemyY=0;
			while (enemyX==0 && enemyY==0) {
				enemyX = Math.floor(Math.random()*size);
				enemyY = Math.floor(Math.random()*size);
				for (var n = 0; n < enemies.length; n++) {
					if (enemies[n][0][0]==enemyX  && enemies[n][0][1]==enemyY) {
						enemyX=0;
						enemyY=0;
					}
				}
			}
			enemies[i] = [ [ enemyX, enemyY ], 3, null, [ space/2, space/2 ], [0,0], null ];		
		}
		clock = setTimeout(function(){ tick() }, 1000/FPS);
	}
	
	init();

	</script>
</body>
</html>
<?php
	}
?>
