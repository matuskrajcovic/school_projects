//Matúš Krajčovič, ID:103003, cvičenie: PON 17:00

const fs = require('fs');
const ws = require('ws');
const crypto = require('crypto');
const express = require('express');
const session = require('express-session');
const uuid = require('uuid');
const urlParser = require('url-parse');
const formidable = require('formidable');
const readline = require('readline');


//Game class uses this function to send data via webSocket
exports.sendData = function sendData(pin, data){

    //sends data to all websockets with given pin 
    //id's are assigned to pins and WS's are assigned to id's
    //also stores maxLevel and maxScore data to session
    findIdsByPin(pin).forEach((id) => {
        if(data.info){
            for(const [key, value] of Object.entries(sessionStore.sessions)){
                let sess = JSON.parse(sessionStore.sessions[key]);
                
                if(sess.uuid && sess.uuid === id){
                    if(sess.maxScore < data.info.score || !sess.maxScore)
                        sess.maxScore = data.info.score;
                    if(sess.maxLevel < data.info.level || !sess.maxLevel)
                        sess.maxLevel = data.info.level;
                    sessionStore.sessions[key] = JSON.stringify(sess);
                    data.maxScore = sess.maxScore;
                    data.maxLevel = sess.maxLevel;
                }
            }
            
        }
        if(idToWs[id])
            idToWs[id].send(JSON.stringify(data));
    });

    //in case user is registered, we update the maxLevel and maxScore data
    findLoginsByPin(pin).forEach((login) => {
        let userIndex = findUserIndexByLogin(login);
        if(userIndex !== null && data.info){
            if(users[userIndex].maxScore < data.info.score)
                users[userIndex].maxScore = data.info.score;
            if(users[userIndex].maxLevel < data.info.level)
                users[userIndex].maxLevel = data.info.level;
        }
    });
}

//Game class import
const Game = require('./game.js').Game;

//User class for registered users
class User {
    constructor(login, password, mail, fullName) {
        this.fullName = fullName;
        this.login = login;
        this.password = password;
        this.mail = mail;
        this.maxScore = 0;
        this.maxLevel = 0;
    }
}


//main server and websocket server
const wsServer = new ws.Server({port: 8082});
const hostname = '127.0.0.1';
const port = 8080;


//global variables, with admin already in it
let users = [];
let games = [];
users.push(new User('admin', crypto.createHash('md5').update("admin").digest('hex'), 'admin', 'admin'));
let idToWs = {};


//session settings
let sessionStore = new session.MemoryStore();
let sessionParser = session({
    secret: 'jmsephfcaonfawo',
    resave: false,
    saveUninitialized: true,
    store: sessionStore
});


//app initiation
let app = express();
app.use(sessionParser);
app.use(express.json());


//app routing
app.get('/', (req, res) => {
    res.setHeader('Content-Type', 'text/html');
    fs.readFile('index.html', 'utf-8', (err,data) => {
        if(err)
            res.status(500).send(err);
        else
            res.status(200).send(data);
    }); 
});

app.get('/client.js', (req, res) => {
    fs.readFile('client.js', 'utf-8', (err,data) => {
        if(err)
            res.status(500).send(err);
        else
            res.status(200).send(data);
    });
});


//send the page layout in a JSON
app.get('/space', (req, res) => {

    //assign uuid if not assigned
    if(!req.session.uuid)
        req.session.uuid = uuid.v4();
    
    //if pin is assigned, remove it and remove the running game (if is running)
    if(req.session.currentPin){
        let gameIndex = findGameIndexByPin(req.session.currentPin);
        req.session.currentPin = null;
        if(gameIndex !== null){
            games[gameIndex].handleReset();
            games.splice(gameIndex, 1);
        }
    }

    res.setHeader('Content-Type', 'application/json');

    //esit data JSON and send the HTML content
    let data = init;
    data[1].html = req.session.fullName ? "USER: " + req.session.fullName : "";
    data[2].html = req.session.currentPin ? "PIN: " + req.session.currentPin : "";
    data[3].html = req.session.maxScore ? "MAX SCORE: " + req.session.maxScore : "MAX SCORE: 0";
    data[4].html = req.session.maxLevel ? "  MAX LEVEL: " + req.session.maxLevel : "  MAX LEVEL: 1";
    res.status(200).send(JSON.stringify(data));
});


//starting the game
app.get('/start', (req, res) => {

    let gameIndex = null;

    //if pin is set and the game is not running, remove it
    if(req.session.currentPin){
        gameIndex = findGameIndexByPin(req.session.currentPin);
        if(gameIndex === null)
            req.session.currentPin = null;
    }

    //if pin is not set, create new game with unique pin
    if(!req.session.currentPin){
        const pin = getPin();
        req.session.currentPin = pin;
        games.push(new Game(pin));
        gameIndex = findGameIndexByPin(pin);
        if(!games[gameIndex].running){
            games[gameIndex].handleReset();
            games[gameIndex].gameLoop();
        }
        res.setHeader('Content-Type', 'application/json');
        res.status(200).send(JSON.stringify({"id": req.session.uuid, "pin": req.session.currentPin}));
    }
    res.status(400).send();
});


//resetting the game
app.get('/reset', (req, res) => {

    //remove the game if running
    let gameIndex = findGameIndexByPin(req.session.currentPin);
    if(gameIndex !== null){
        games[gameIndex].handleReset();
        games.splice(gameIndex, 1);
    }
    req.session.currentPin = null;
    res.status(200).send();
});


//handles incoming keydown data
app.post('/keydown', (req, res) => {

    //find corresponding game and send key code
    let gameIndex = findGameIndexByPin(req.session.currentPin);
    if(gameIndex !== null)
        games[gameIndex].checkKey(req.body.key);
    res.status(200).send();
});


//handles login
app.post('/login', (req, res) => {

    //login the user if credidentials are okay
    let userIndex = findRegisteredUserIndex(req.body.login, req.body.password);
    if(userIndex !== null && !req.session.login){
        req.session.login = req.body.login;
        req.session.fullName = users[userIndex].fullName;
        let output = {
            "fullName":users[userIndex].fullName, 
            "maxScore" : users[userIndex].maxScore, 
            "maxLevel" : users[userIndex].maxLevel, 
            "login" : users[userIndex].login
        };
        req.session.save();

        //if user is admin, send adminTable data
        if(req.body.login === 'admin'){
            output.adminData = adminTable();
        }
        res.status(200).send(JSON.stringify(output));
    }
    res.status(400).send();
});


//handles register
app.post('/register', (req, res) => {

    //if registration credidentials are ok, register the user
    if(!req.session.login && checkRegistration(req.body)){
        let user = new User(req.body.login, crypto.createHash('md5').update(req.body.password1).digest('hex'), req.body.mail ,req.body.fullName);
        users.push(user);
        req.session.login = req.body.login;
        req.session.fullName = req.body.fullName;
        req.session.save();
        res.status(200).send(JSON.stringify({"fullName":req.body.fullName, "maxScore" : user.maxScore, "maxLevel" : user.maxLevel}));
    }
    res.status(400).send();
});


//handles joining into running game from another session
app.post('/join', (req, res) => {
    if(/^[0-9][0-9][0-9][0-9]$/.test(req.body.pin) && !checkPin(req.body.pin) && req.body.pin !== req.session.currentPin){
        
        //end current game
        let gameIndex = findGameIndexByPin(req.session.currentPin);
        if(gameIndex){
            games[gameIndex].handleReset();
            games.splice(gameIndex, 1);
        }
        
        //set the new pin
        req.session.currentPin = req.body.pin;
        res.status(200).send(JSON.stringify({"id": req.session.uuid}));
    }
    res.status(400).send();
});


//handles leaving from the joined game
app.get('/leave', (req, res) => {

    //if the pin is set, remove it
    if(req.session.currentPin){

        //if the user is last, delete the game
        if(findIdsByPin(req.session.currentPin).length <= 1){
            let gameIndex = findGameIndexByPin(req.session.currentPin);
            games[gameIndex].handleReset();
            games.splice(gameIndex, 1);
        }
        req.session.currentPin = null;
        res.status(200).send();
    }
    res.status(400).send();
});


//logging out
app.get('/logout', (req, res) => {
    if(req.session.login){
        delete req.session.login;
        delete req.session.fullName;
        res.status(200).send();
    }
    res.status(400).send();
});


//exporting user and game data for admin
app.get('/export', (req, res) => {
    if(req.session.login == 'admin'){
        res.setHeader('Content-Type', 'text/csv');
        res.setHeader("Content-Disposition", "attachment;filename=export.csv");
        res.status(200).send(exportCsv());
    }
    res.status(400).send();
});


//importing user data for admin
app.post('/import', (req, res) => {
    let form = new formidable.IncomingForm();
    form.parse(req, (err, fields, files) => {
        res.end();
        importCsv(files.file.filepath);
    });
});


//launch the server
app.listen(port, hostname, () => {
    console.log('Server running');
});



//ws server initiate
//keeps 'uuid' => 'ws' associations
wsServer.on('connection', (ws, req) => {

    const id = urlParser(req.url, true).query.id;
    idToWs[id] = ws;

    ws.on('close', () => {
        delete idToWs[id];
    });
});



//help functions for finding games, users or ids
function findIdsByPin(pin){
    let ret = [];
    for(const [key, value] of Object.entries(sessionStore.sessions)){
        let data = JSON.parse(sessionStore.sessions[key]);
        if(data.currentPin && data.uuid && data.currentPin == pin)
            ret.push(data.uuid);
    }
    return ret;
}

function findLoginsByPin(pin){
    let ret = [];
    for(const [key, value] of Object.entries(sessionStore.sessions)){
        let data = JSON.parse(sessionStore.sessions[key]);
        if(data.currentPin && data.login && data.currentPin == pin)
            ret.push(data.login);
    }
    return ret;
}

function findUserIndexByLogin(login){
    for(let i = 0; i<users.length; i++)
        if (users[i].login === login)
            return i;
}

function findGameIndexByPin(pin){
    if (!pin) 
        return null;
    for(let i = 0; i < games.length; i++)
        if(games[i].pin === pin)
            return i;
    return null;
}

function findRegisteredUserIndex(login, password) {
    for(let i = 0; i < users.length; i++)
        if(users[i].login === login && users[i].password === crypto.createHash('md5').update(password).digest('hex'))
            return i;
    return null;
}


//checks, if the pin is avaliable
function checkPin(pin) {
    for(let i = 0; i < games.length; i++){
        if(games[i].pin === pin)
            return false;
    }
    for(const [key, value] of Object.entries(sessionStore.sessions)){
        let sess = JSON.parse(sessionStore.sessions[key]);
        if(sess.currentPin && sess.currentPin === pin)
            return false;
    }
    return true;
}

//assings new pin
function getPin(){
    for(let i = 0; i <= 9999; i++){
        let pin = i.toString().padStart(4, '0');
        if(checkPin(pin))
            return pin;
    }
    return null;
}

//checks registration data
function checkRegistration(data) {
    if(data.password1 !== data.password2)
        return false;
    if(!/^[A-Z]([a-zA-Z]+) [A-Z]([a-zA-Z]+)$/.test(data.fullName) || !(/^.+@.+\..+$/.test(data.mail)) || !(/^[a-zA-Z]+$/.test(data.login)) || /^$/.test(data.password1))
        return false;
    for (let i = 0; i < users.length; i++)
        if(users[i].mail === data.mail || users[i].login === data.login)
            return false;
    return true;
}


//export to csv
function exportCsv(){
    let fieldsUsers = ['fullName', 'login', 'password', 'mail', 'maxScore', 'maxLevel'];
    let csv = '';
    fieldsUsers.forEach((field) => {
        csv += field + ';'
    });
    csv += '\n';
    users.forEach((user) => {
        fieldsUsers.forEach((field) => {
            csv += user[field] + ';';
        });
        csv += '\n';
    });
    return csv;
}


//import from csv
function importCsv(data){
    const lineReader = readline.createInterface({
        input: fs.createReadStream(data)
      });
      users = [];
      lineReader.on('line', (line) => {
        if(!line.startsWith('fullName') && /^[A-Z]([a-zA-Z]+) [A-Z]([a-zA-Z]+);[a-zA-Z]+;.+;.+@.+\..+;[0-9]*;[0-9]*;/.test(line)){
            let splitted = line.split(';');
            let user = new User(splitted[1], splitted[2], splitted[3], splitted[0]);
            user.maxScore = splitted[4];
            user.maxLevel = splitted[5];
            users.push(user);
        }
      });
}


//admin function to return current users and game table
function adminTable(){
    let fieldsUsers = ['fullName', 'login', 'password', 'mail', 'maxScore', 'maxLevel', 'uuid', 'currentPin'];
	let fieldsGames = ['pin', 'score', 'level', 'uuidUser', 'loginUser'];

    let users_table = [];
    let games_table = [];

    let row = {
        "tag" : "tr",
        "inner" : []
    };
    fieldsUsers.forEach((field) => {
        row.inner.push({
            "tag": "th",
            "html" : field
        });
    });
    users_table.push(row);

    users.forEach((user) => {
        row = {
            "tag" : "tr",
            "inner" : []
        };
        fieldsUsers.forEach((field) => {
            row.inner.push({
                "tag": "td",
                "html" : user[field] ? user[field] : "NaN"
            });
        });
        users_table.push(row);
    });

    for(const [key, value] of Object.entries(sessionStore.sessions)){
        let data = JSON.parse(sessionStore.sessions[key]);
        if(data.uuid){
            if(data.login){
                users_table.forEach((user) => {
                    if(user.inner[1].html === data.login){
                        user.inner[6].html = data.uuid;
                        if(data.currentPin)
                            user.inner[7].html = data.currentPin;
                    }
                });
            }
            else{
                let row = {
                    "tag" : "tr",
                    "inner" : []
                };
                fieldsUsers.forEach((field) => {
                    row.inner.push({
                        "tag": "td",
                        "html" : data[field] ? data[field] : "NaN"
                    });
                });
                users_table.push(row);
            }
        }
    }

    row = {
        "tag" : "tr",
        "inner" : []
    };
    fieldsGames.forEach((field) => {
        row.inner.push({
            "tag": "th",
            "html" : field
        });
    });
    games_table.push(row);

    games.forEach((game) => {
        row = {
            "tag" : "tr",
            "inner" : []
        };
        fieldsGames.forEach((field) => {
            row.inner.push({
                "tag": "td",
                "html" : game[field] ? game[field] : "NaN"
            });
        });
        games_table.push(row);
    });
    let output = admin;
    output[0].inner[1].inner = users_table;
    output[0].inner[3].inner = games_table;
    return output;
}


//admin data template
let admin = [
{
		"tag" : "div",
		"id" : "admin_panel",
        "inner" : [
            {
                "tag" : "h3",
                "html" : "ACTIVE USERS"
            },
            {
                "tag" : "table",
                "inner" : []
            },
            {
                "tag" : "h3",
                "html" : "ACTIVE GAMES"
            },
            {
                "tag" : "table",
                "inner" : []
            },
            {
                "tag" : "button",
                "id" : "export",
                "html" : "EXPORT TO CSV"
            },
            {
                "tag" : "button",
                "id" : "import",
                "html" : "IMPORT FROM CSV"
            },
            {
                "tag" : "input",
                "id" : "import_file",
                "attr" : {
					"type" : "file",
                    "name" : "file"
				}
            }
        ]
	},
];



//html elements sent on page load
let init = [
	{
		"tag" : "h1",
		"html" : "Vesmirna Hra"
	},
	{
		"tag" : "h2",
		"id" : "user"
	},
	{
		"tag" : "h3",
		"id" : "current_pin"
	},
    {
		"tag" : "span",
		"id" : "max_score"
	},
    {
		"tag" : "span",
		"id" : "max_level"
	},
	{
		"tag" : "div",
		"id" : "space",
		"inner" : [
			{
				"tag" : "canvas",
				"id" : "canvas",
				"attr" : {
					"width" : 528,
					"height" : 528
				}
			}
		]
	},
	{
		"tag" : "button",
		"id" : "start",
		"html" : "START"
	},
	{
		"tag" : "button",
		"id" : "reset",
		"html" : "RESET"
	},
	{
		"tag" : "button",
		"id" : "music",
		"html" : "&#9834; PLAY MUSIC &#9834;"
	},
	{
		"tag" : "span",
		"id" : "level",
		"html" : "LEVEL: 0"
	},
	{
		"tag" : "span",
		"id" : "score",
		"html" : "SCORE: 0"
	},
	{
		"tag" : "audio",
        "id" : "audio",
		"inner" : [
			{
				"tag" : "source",
				"attr" : {
					"src": "http://free-loops.com/data/mp3/76/5f/78f4cdeecb213536db839e684ae1.mp3",
					"type": "audio/mp3"
				}
			}
		]
	},
	{
		"tag" : "div",
		"inner" : [
			{
				"tag" : "input",
				"id" : "pin",
				"attr" : {
					"type": "text"
				}
			},
			{
				"tag" : "button",
				"id" : "join",
				"html" : "JOIN"
			},
			{
				"tag" : "button",
				"id" : "leave",
				"html" : "LEAVE"
			}
		]
	},
	{
		"tag" : "div",
		"inner" : [
			{
				"tag" : "input",
				"id" : "register_name",
				"attr" : {
					"type": "text",
					"placeholder" : "NAME"
				}
			},
			{
				"tag" : "input",
				"id" : "register_mail",
				"attr" : {
					"type": "text",
					"placeholder" : "MAIL"
				}
			},
			{
				"tag" : "input",
				"id" : "register_login",
				"attr" : {
					"type": "text",
					"placeholder" : "LOGIN"
				}
			},
			{
				"tag" : "input",
				"id" : "register_password1",
				"attr" : {
					"type": "text",
					"placeholder" : "PASSWORD"
				}
			},
			{
				"tag" : "input",
				"id" : "register_password2",
				"attr" : {
					"type": "text",
					"placeholder" : "PASSWORD"
				}
			},
			{
				"tag" : "button",
				"id" : "register",
				"html" : "REGISTER"
			}
		]
	},
	{
		"tag" : "div",
		"inner" : [
			{
				"tag" : "input",
				"id" : "login_login",
				"attr" : {
					"type": "text",
					"placeholder" : "LOGIN"
				}
			},
			{
				"tag" : "input",
				"id" : "login_password",
				"attr" : {
					"type": "text",
					"placeholder" : "PASSWORD"
				}
			},
			{
				"tag" : "button",
				"id" : "login",
				"html" : "LOGIN"
			}
		]
	},
	{
		"tag" : "button",
		"id" : "logout",
		"html" : "LOGOUT"
	},
	{
		"tag" : "img",
		"id" : "alien",
		"attr" : {
			"src" : "https://www.svgrepo.com/show/186671/alien.svg",
			"style" : "display:none;"
		}
	},
	{
		"tag" : "img",
		"id" : "bullet",
		"attr" : {
			"src" : "https://www.svgrepo.com/show/5490/bullet.svg",
			"style" : "display:none;"
		}
	},
	{
		"tag" : "img",
		"id" : "ship",
		"attr" : {
			"src" : "https://www.svgrepo.com/show/222148/spaceship.svg",
			"style" : "display:none;"
		}
	},
	{
		"tag" : "img",
		"id" : "trophey",
		"attr" : {
			"src" : "https://www.svgrepo.com/show/41724/trophey.svg",
			"style" : "display:none;"
		}
	},
	{
		"tag" : "img",
		"id" : "sad",
		"attr" : {
			"src" : "https://www.svgrepo.com/show/48636/sad.svg",
			"style" : "display:none;"
		}
	}
]