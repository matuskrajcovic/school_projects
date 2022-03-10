//Matúš Krajčovič, ID:103003, cvičenie: PON 17:00

/*
    pictures: all with CC0 licence (https://www.svgrepo.com/)
        links:
            https://www.svgrepo.com/svg/186671/alien
            https://www.svgrepo.com/svg/222148/spaceship
            https://www.svgrepo.com/svg/5490/bullet
            https://www.svgrepo.com/svg/48636/sad
            https://www.svgrepo.com/svg/41724/trophey
    audio: also CC licenced (http://free-loops.com/8618-melodic-dubstep-140.html)
        Artist: Michael Quiroz
        i didn't make any changes to the sound
*/


window.onload = () => {
	initSpace();
};


//fetch /space from server and initiate all event listeners
function initSpace() {

	fetch('http://localhost:8080/space')
	.then(response => response.json())
	.then(data => {

		//parse html content from server
		htmlParser(data, document.body);

		//set event listeners for each button
		document.getElementById('start').addEventListener('keydown',function(e){
			e.preventDefault();
			e.stopPropagation();
		});

		document.getElementById('start').addEventListener('click', () => {
			fetch('http://localhost:8080/start')
				.then(response => response.json())
				.then(data => {
					document.getElementById('current_pin').innerHTML = 'PIN:' + data.pin;
					const address = 'ws://localhost:8082?id=' + data.id;
					initWs(address);
					document.addEventListener('keydown', handleKeyDown);
				}).catch((err) => {})
		});

		document.getElementById('reset').addEventListener('click', () => {
			fetch('http://localhost:8080/reset').then(() => {
				document.getElementById('current_pin').innerHTML = '';
				socket.close();
				socket = null;
				document.removeEventListener('keydown', handleKeyDown);
			}).catch((err) => {})
		});

		document.getElementById('join').addEventListener('click',() => {
			sendPost('http://localhost:8080/join', {
				"pin" : document.getElementById('pin').value
			})
			.then(response => response.json())
			.then(data => {
				document.removeEventListener('keydown', handleKeyDown);
				document.addEventListener('keydown', handleKeyDown);
				document.getElementById('current_pin').innerHTML = 'PIN: ' + document.getElementById('pin').value;
				socket = null;
				const address = 'ws://localhost:8082?id=' + data.id;
				initWs(address);
				
			}).catch((err) => {})
		});

		document.getElementById('leave').addEventListener('click',() => {
			fetch('http://localhost:8080/leave')
			.then(response => {
				if(response.status == 200){
					document.removeEventListener('keydown', handleKeyDown);
					document.addEventListener('keydown', handleKeyDown);
					socket = null;
					document.getElementById('current_pin').innerHTML = '';
				}
			}).catch((err) => {})
		});

		document.getElementById('register').addEventListener('click',() => {
			sendPost('http://localhost:8080/register', {
				"login" : document.getElementById('register_login').value,
				"fullName" : document.getElementById('register_name').value,
				"mail" : document.getElementById('register_mail').value,
				"password1" : document.getElementById('register_password1').value,
				"password2" : document.getElementById('register_password2').value
			})
			.then(response => response.json())
			.then(data => {
				document.getElementById('user').innerHTML = 'USER: ' + data.fullName;
				document.getElementById('max_score').innerHTML = 'MAX SCORE: ' + data.maxScore;
				document.getElementById('max_level').innerHTML = '  MAX LEVEL: ' + data.maxLevel;
			}).catch((err) => {});
		});

		document.getElementById('login').addEventListener('click',() => {
			let login = document.getElementById('login_login').value;
			let pass = document.getElementById('login_password').value;
			sendPost('http://localhost:8080/login', {
				"login" : login,
				"password" : pass
			})
			.then(response => response.json())
			.then(data => {
				document.getElementById('user').innerHTML = 'USER: ' + data.fullName;
				document.getElementById('max_score').innerHTML = 'MAX SCORE: ' + data.maxScore;
				document.getElementById('max_level').innerHTML = '  MAX LEVEL: ' + data.maxLevel;

				//if data contains admin info, print it and create corresponding event listeners
				if(data.adminData){
					htmlParser(data.adminData, document.body);
					document.getElementById('export').addEventListener('click', () => {
						fetch('http://localhost:8080/export')
						.then(response => response.text())
						.then(data => {
							const link = document.createElement("a");
							link.href = URL.createObjectURL(new Blob([data], { type: 'text/csv;charset=utf-8;' }));
							link.download = 'export.csv';
							link.style.visibility = 'hidden';
							document.body.appendChild(link);
							link.click();
							document.body.removeChild(link);
						})
						.catch((err) => {})
					});
					document.getElementById('import').addEventListener('click', () => {
						if(document.getElementById('import_file').value !== ''){
							let data = new FormData();
							data.append('file', document.getElementById('import_file').files[0]);
							fetch('http://localhost:8080/import', {
								method: 'POST',
								body: data
							}).catch((err) => {});
						}
						else
							alert('select file');
					});
				}
			}).catch((err) => {})
		});

		document.getElementById('logout').addEventListener('click',() => {
			fetch('http://localhost:8080/logout').catch((err) => {});
			document.getElementById('user').innerHTML = '';
			document.getElementById('max_score').innerHTML = '';
			document.getElementById('max_level').innerHTML = '';
			let admin = document.getElementById('admin_panel');
			if(admin)
				document.body.removeChild(admin);
		});

		document.getElementById('music').addEventListener('click', () => {
			let audioElem = document.getElementsByTagName('audio')[0];
			if (!audioElem.paused) {
				audioElem.pause();
				audioElem.currentTime = 0;
				document.getElementById('music').innerHTML = "&#9834; PLAY MUSIC &#9834;";
			}
			else {
				audioElem.volume = 1;
				audioElem.play();
				document.getElementById('music').innerHTML = "PAUSE MUSIC";
			}
		});
	}).catch((err) => {});
}


function handleKeyDown(ev){
	sendPost('http://localhost:8080/keydown', {
		"key" : ev.keyCode
	});
}


//parse incoming HTML data in JSON and append to a parent
function htmlParser(data, parent){
	data.forEach(el=> {
		let temp = document.createElement(el.tag);
		if(el.id)
			temp.id = el.id;
		if(el.html)
			temp.innerHTML = el.html;
		if(el.attr)
			for(let [key, val] of Object.entries(el.attr))
				temp.setAttribute(key, val);
		parent.appendChild(temp);
		if(el.inner)
			htmlParser(el.inner, temp);
	});
}


//websocket
let socket = null;


//initiation of the websocket with given query
function initWs(address){
	socket = new WebSocket(address);
	socket.addEventListener('message', (ev) => {
		let data = JSON.parse(ev.data);
		switch(data['name']) {
			case 'win':
				win();
				break;
			case 'loose':
				loose();
				break;
			case 'drawSpace':
				drawSpace(data);
				break;
			case 'drawAliens':
				drawAliens(data);
				break;
			case 'drawMissiles':
				drawMissiles(data);
				break;
			case 'drawShip':
				drawShip(data);
				break;
			default:
		}
	});
}


//POST help function
function sendPost(url, data){
	return fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify(data)
	}).catch((err) => {});
}


//original functions to draw the game and handle the sound
function drawSpace(data) {
    document.getElementById('level').innerHTML = ' Level: ' + data['info']['level'];
    document.getElementById('score').innerHTML = ' Score: ' + data['info']['score'];
	if(data.maxScore !== undefined)
		document.getElementById('max_score').innerHTML = 'MAX SCORE: ' + data.maxScore;
	if(data.maxLevel !== undefined)
		document.getElementById('max_level').innerHTML = '  MAX LEVEL: ' + data.maxLevel;
    fillCanvas(data['info']['color']);
}

function drawAliens(data) {
    const ctx = document.getElementById('canvas').getContext('2d');
    for(let i=0;i<data['aliens'].length;i++)
        ctx.drawImage(document.getElementById('alien'), 0 + (data['aliens'][i] % 11)*48, 0 + Math.floor(data['aliens'][i] / 11)*48, 48, 48); 
}

function drawMissiles(data) {
    const ctx = document.getElementById('canvas').getContext('2d');
    for(let i=0;i<data['missiles'].length;i++)
        ctx.drawImage(document.getElementById('bullet'), 0 + (data['missiles'][i] % 11)*48, 0 + Math.floor(data['missiles'][i] / 11)*48, 48, 48); 
}

function drawShip(data) {
    document.getElementById('canvas').getContext('2d').drawImage(document.getElementById('ship'), 0 + (data['ship'][0] % 11)*48 - 24, 0 + Math.floor(data['ship'][0] / 11)*48, 96, 96);
}

function win() {
    fillCanvas('green');
    fillImage(document.getElementById('trophey'));
	if(checkDebug())
		console.log('win');
}

function loose() {
    fillCanvas('red');
    fillImage(document.getElementById('sad'));
	if(checkDebug())
		console.log('lose');
}


//fills whole canvas with a color
function fillCanvas(color) {
    const cnv = document.getElementById('canvas');
    const ctx = cnv.getContext('2d');
    ctx.fillStyle = color;
    ctx.fillRect(0,0,cnv.width,cnv.height);
}

//fills whole canvas with a picture
function fillImage(img) {
    const cnv = document.getElementById('canvas');
    const ctx = cnv.getContext('2d');
    ctx.drawImage(img, 0, 0, cnv.width, cnv.height);
}

function checkDebug(){
	return (
		String(window.location.search).includes('debug') ||
		window.localStorage.getItem('debug') ||
		window.sessionStorage.getItem('debug') ||
		document.cookie.indexOf('debug=') > -1 ||
		typeof debug !== 'undefined'
	);
}
