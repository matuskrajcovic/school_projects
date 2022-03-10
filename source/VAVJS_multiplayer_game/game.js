//Matúš Krajčovič, ID:103003, cvičenie: PON 17:00

const sendData = require('./server.js').sendData;

exports.Game = class Game {
    constructor(pin){
        this.aliens = [1,3,5,7,9,23,25,27,29,31];
        this.direction = 1;
        this.ship = [104,114,115,116];
        this.missiles = [];
        this.score = 0;
        this.level = 1;
        this.speed = 512;
        this.running = false;
        this.loop1 = null;
        this.loop2 = null;
        this.timeout = null;
		this.pin = pin;
    }

    moveAliens() {
        let i=0;
        for(i=0;i<this.aliens.length;i++) {
            this.aliens[i]=this.aliens[i]+this.direction;
        }
        this.direction *= -1;
    }

    lowerAliens() {
        var i=0;
        for(i=0;i<this.aliens.length;i++) {
            this.aliens[i]+=11;
        }
    }

    moveMissiles() {
        var i=0;
        for(i=0;i<this.missiles.length;i++) {
            this.missiles[i]-=11 ;
            if(this.missiles[i] < 0) this.missiles.splice(i,1);
        }
    }

    checkCollisionsMA() {
        for(var i=0;i<this.missiles.length;i++) {
            if(this.aliens.includes(this.missiles[i])) {
                var alienIndex = this.aliens.indexOf(this.missiles[i]);
                this.aliens.splice(alienIndex, 1);
                this.missiles.splice(i, 1);
                this.score += 10;
                //if (checkDebug()) console.log('Alien hit.');
            }
        }
    }

    RaketaKolidujeSVotrelcom() {
        for(var i=0;i<this.aliens.length;i++) {
            if(this.aliens[i]>98) {
                return true;
            }
        }
        return false;
    }

    nextLevel() {
        this.level++;
        console.log('level: '+this.level);
        if(this.level==1) this.aliens = [1,3,5,7,9,23,25,27,29,31];
        if(this.level==2) this.aliens = [1,3,5,7,9,13,15,17,19,23,25,27,29,31];
        if(this.level==3) this.aliens = [1,5,9,23,27,31];
        if(this.level==4) this.aliens = [45,53];
        if(this.level > 4) {
            this.level = 1;
            this.aliens = [1,3,5,7,9,23,25,27,29,31];
            this.speed = this.speed / 2;
        }
        this.gameLoop();
    }

    checkKey(keyCode) {
        if (keyCode == '37' || keyCode == '65') {
            if(this.ship[0] > 100) {
                var i=0;
                for(i=0;i<this.ship.length;i++) {
                    this.ship[i]--;
                }
            }
        }
        else if ((keyCode == '39' || keyCode == '68') && this.ship[0] < 108) {
            var i=0;
            for(i=0;i<this.ship.length;i++) {
                this.ship[i]++;
            }
        }
        else if (keyCode == '32') {
            this.missiles.push(this.ship[0]-11);
        }
    }

    drawSpace() {
        let data = {
            "name" : "drawSpace",
            "info" : {
                "score" : this.score,
                "level" : this.level,
                "color" : "#e0e0e0"
            }
        };
        sendData(this.pin, data);
    }

    drawAliens() {
        let data = {
            "name" : "drawAliens",
            "aliens" : this.aliens
        };
        sendData(this.pin, data);
    }

    drawMissiles() {
        let data = {
            "name" : "drawMissiles",
            "missiles" : this.missiles
        };
        sendData(this.pin, data);
    }

    drawShip() {
        let data = {
            "name" : "drawShip",
            "ship" : this.ship
        };
        sendData(this.pin, data);
    }

    win() {
        //if (checkDebug()) console.log('win');
        let data = {
            "name" : "win"
        };
        sendData(this.pin, data);
    }

    loose() {
        this.running=false;
        //if (checkDebug()) console.log('loose');
        let data = {
            "name" : "loose"
        };
        sendData(this.pin, data);
    }

    //handles the reset button
    handleReset(){

        clearInterval(this.loop1);
        clearInterval(this.loop2);
        clearTimeout(this.timeout);
        
        this.missiles=[];
        this.aliens=[1,3,5,7,9,23,25,27,29,31];
        this.direction=1;
        this.ship=[104,114,115,116];
        this.level=1;
        this.score=0;
        this.speed=512;

        this.drawSpace();

        //document.removeEventListener('keydown',checkKey);
        this.running=false;
        console.log('reset');

        //if (checkDebug()) console.log('game reset and stopped');
    }

    loop1F(a){
        this.moveAliens();
        this.moveMissiles();
        this.checkCollisionsMA();
        if(a.value%4==3) this.lowerAliens();
        if(this.RaketaKolidujeSVotrelcom()) {
            clearInterval(this.loop2);
            clearInterval(this.loop1);
            //document.removeEventListener('keydown',checkKey);
            this.missiles = [];
            this.drawMissiles();
            this.drawSpace();
            this.loose();
        }
        a.value++;
    }

    loop2F(){
        this.drawSpace();
        this.drawAliens();
        this.drawMissiles();
        this.drawShip();
        if(this.aliens.length === 0) {
            clearInterval(this.loop2);
            clearInterval(this.loop1);
            //document.removeEventListener('keydown',checkKey);
            this.missiles = [];
            this.drawMissiles();
            this.drawSpace();
            this.win();
            this.timeout = setTimeout(() => {this.timeoutF();},1000);
        }
    }

    timeoutF(){
        this.nextLevel();
    }

    gameLoop() {
        console.log('gameloop');

        this.running = true;

        var a = {'value' : 0};
        this.loop1 = setInterval(() => { this.loop1F(a); },this.speed);
        this.loop2 = setInterval(() => { this.loop2F(); },this.speed/2);
    }
}