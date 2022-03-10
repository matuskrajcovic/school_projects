//Matus Krajcovic, ID: 103003

const fs = require('fs');
const express = require('express');
const mysql = require('mysql');

//initiate express app
let app = express();
app.use(express.json());
app.listen(8080, () => {
	console.log('Server running');
	connectDB();
});

//initiate DB, create tables and insert 3 products and an ad
let connectionMade = false;
let connection = null;
function connectDB() {
	if(connectionMade===false) {
		connection = mysql.createConnection({
			//host : 'localhost',
			host : 'mydb',
			user : 'root',
			//password : 'password',
			password : 'root',
			database : 'zad3'
		});
		connection.connect((err) =>  {
			if(!err) {
				connectionMade = true;
			}
		});
	}
}

//get files
app.get('/', (req, res) => {
	res.setHeader('Content-Type', 'text/html');
	res.setHeader('Access-Control-Allow-Origin','*');
	fs.readFile('static/index.html','utf-8',(err,data)=>{
		if(err)
			res.status(500).send('<html><body><h1>FS ERROR</h1></body></html>');
		else
			res.status(200).send(data);
	});
});

app.get('/bundle.js', (req, res) => {
	res.setHeader('Content-Type', 'text/html');
	res.setHeader('Access-Control-Allow-Origin','*');
	fs.readFile('static/bundle.js','utf-8',(err,data)=>{
		if(err)
			res.status(500).send('<html><body><h1>FS ERROR</h1></body></html>');
		else
			res.status(200).send(data);
	});
});

app.get('/(:type)/(:file).jpg', (req, res) => {
	res.setHeader('Content-Type', 'text/html');
	res.setHeader('Access-Control-Allow-Origin','*');
	res.setHeader('Content-Type', 'image/jpeg');
	fs.readFile('static/' + req.params.type + '/' + req.params.file + '.jpg',(err,data)=>{
		if(err)
			res.status(500).send('<html><body><h1>FS ERROR</h1></body></html>');
		else
			res.status(200).send(data);
	});
});

app.get('/(:type)/(:file).png', (req, res) => {
	res.setHeader('Content-Type', 'text/html');
	res.setHeader('Access-Control-Allow-Origin','*');
	res.setHeader('Content-Type', 'image/png');
	fs.readFile('static/' + req.params.type + '/' + req.params.file + '.png',(err,data)=>{
		if(err)
			res.status(500).send('<html><body><h1>FS ERROR</h1></body></html>');
		else
			res.status(200).send(data);
	});
});


//requests
app.get('/products', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	connection.query('SELECT * FROM products;', function (error, results, fields) {
		if (error)
			res.status(500).send({"error" : error});
		else
			res.status(200).send(results);
	});
});

app.post('/order', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	let check = checkOrder(req.body.customer);
	if(check){
		connection.query('INSERT INTO customers (email, name, street, number, city, postal) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE email=email;', [
			req.body.customer.email, req.body.customer.name, req.body.customer.street, req.body.customer.number, req.body.customer.city, req.body.customer.postal
		], function (error, results, fields) {
			if (error) res.status(500).send({"error" : "insert into customers"});// return null;}
			else {
				connection.query('SELECT id FROM customers WHERE email = ?;', [req.body.customer.email], function (error, results, fields) {
					if (error) res.status(500).send({"error" : "select from customers"});// return null;}
					else if (results[0] === undefined) res.status(500).send({"error" : "no customer with mail"});// return null;}
					else{
						let customer_id = results[0].id;
						connection.query('INSERT INTO orders (customer_id, done) VALUES (?, 0);', [customer_id], function (error, results, fields) {
							if (error) res.status(500).send({"error" : "insert into orders"});// return null;}
							else {
								connection.query('SELECT id FROM orders WHERE customer_id = ? ORDER BY id DESC LIMIT 1;', [customer_id], function (error, results, fields) {
									if (error) res.status(500).send({"error" : "no order with customer id"});// return null;}
									else if (results[0] === undefined) res.status(500).send({"error" : "no order with customer id"});
									else {
										let order_id =  results[0].id;
										for(let i = 0; i < req.body.items.length; i++){
											connection.query('INSERT INTO order_product (product_id, order_id, count) VALUES (?, ?, ?);', [req.body.items[i].id, order_id, req.body.items[i].count], function (error, results, fields) {
												if (error) {
													res.status(500).send({"error" : "insert into order_product"}); 
													return null;
												}
												if(i === req.body.items.length - 1)
													res.status(200).send({});
											});
										}
									}
								});
							}
						});
					}
				});	
			}
		});
	}
	else
		res.status(500).send({"error" : "wrong format"});
});

app.get('/ad', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	connection.query('SELECT * FROM ads LIMIT 1;', function (error, results, fields) {
		if (error)
			res.status(500).send({"error" : error});
		else
			res.status(200).send(results);
	});
});

app.post('/clickAd', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	connection.query('UPDATE ads SET counter = counter + 1 WHERE id=?;', [req.body.id] ,function (error, results, fields) {
		if (error)
			res.status(500).send({"error" : error});
		else
			res.status(200).send({"counter" : req.body.counter + 1});
	});
});

app.get('/admin', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	connection.query('SELECT o.id as id, c.id as customer_id, c.name, c.email, o.done FROM orders o JOIN customers c ON o.customer_id=c.id;', function (error, results, fields) {
		if (error)
			res.status(500).send({"error" : error});
		else
			res.status(200).send(results);
	});
});

app.post('/markAsDone', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	connection.query('UPDATE orders SET done = 1 WHERE id=?;', [req.body.id], function (error, results, fields) {
		if (error)
			res.status(500).send({"error" : error});
		else
			res.status(200).send({});
	});
});

app.post('/changeAd', async (req, res) => {
	await connectDB();
	res.setHeader('Access-Control-Allow-Origin','*');
	if(req.body.link && req.body.image){
		connection.query('SELECT * FROM ads WHERE id=1;', function (error, results) {
			if (error)
				res.status(500).send({"error" : error});
			else if(results[0]){
				connection.query('UPDATE ads SET link=?, image=? WHERE id=1;', [req.body.link, req.body.image], function (error, results, fields) {
					if (error)
						res.status(500).send({"error" : error});
					else
						res.status(200).send({});
				});
			}
			else{
				connection.query('INSERT INTO ads (id, link, image) VALUES (1, ?, ?);', [req.body.link, req.body.image], function (error, results, fields) {
					if (error)
						res.status(500).send({"error" : error});
					else
						res.status(200).send({});
				});
			}
		});
	}
	else
		res.status(500).send({"error" : "vyplnte oba polia"});
});

app.options(['/order', '/clickAd', '/markAsDone', '/changeAd'], (req, res) => {
	res.setHeader('Access-Control-Allow-Origin','*');
	res.setHeader('Access-Control-Allow-Methods', 'POST');
	res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
	res.status(200).send({});
});

//check if order fields and okay
function checkOrder(data) {
	if(!(/^.+@.+\..+$/.test(data.email)) || /^$/.test(data.name) || /^$/.test(data.street) || !(/^[0-9]+$/.test(data.number)) || /^$/.test(data.city) || !(/^[0-9 ]+$/.test(data.postal)))
		return false;
	else
    	return true;
}
