//Matus Krajcovic, ID: 103003

const assert = require('assert');
const fetch = require("isomorphic-fetch");

describe('mocha tests', async function() {

  //testy na get niektore get requesty
  step('get requests', async function() {
    await describe('products', function() {
        it('should return 3 products', async () => {
          let output = await globalFetch('http://localhost:8080/products');
          assert.equal(Object.keys(output).length, 3);
        });
    });
    await describe('ads', function() {
      it('should return 0 ads (there are none at the beginning)', async () => {
        let output = await globalFetch('http://localhost:8080/ad');
        assert.equal(Object.keys(output).length, 0);
      });
    });
    await describe('admin orders', function() {
      it('should return 0 orders (there are none at the beginning)', async () => {
        let output = await globalFetch('http://localhost:8080/admin');
        assert.equal(Object.keys(output).length, 0);
      });
    });
  });
  
  //testy na objednavky
  step('orders', async function() {
    await describe('dobra objednavka', function() {
        it('should return empty dictionary with length 0', async () => {
          let output = await order(inputs[0]);
          assert.equal(Object.keys(output).length, 0);
        });
    });
    await describe('objednavka so zlym mailom', function() {
      it('should return error on inserting into customers', async () => {
        let output = await order(inputs[1]);
        assert.equal(Object.keys(output).length, 1);
      });
    });
    await describe('objednavka so zlym cislom', function() {
      it('should return error with inserting to order_product', async () => {
        let output = await order(inputs[2]);
        assert.equal(Object.keys(output).length, 1);
      });
    });
    await describe('objednavka so zlym PSC', function() {
      it('should return error with inserting to order_product', async () => {
        let output = await order(inputs[3]);
        assert.equal(Object.keys(output).length, 1);
      });
    });
    await describe('objednavka bez mena', function() {
      it('should return error with inserting to order_product', async () => {
        let output = await order(inputs[4]);
        assert.equal(Object.keys(output).length, 1);
      });
    });
    await describe('objednavka so zlym id produktu', function() {
      it('should return error with inserting to order_product', async () => {
        let output = await order(inputs[5]);
        assert.equal(Object.keys(output).length, 1);
      });
    });
  })
});


async function order(input){
  let output = null;
  await fetch('http://localhost:8080/order', {
    method: "POST",
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(input)
  }).then(response => response.json())
  .then(data => {
    console.log(data);
    output = data;
  })
  .catch(err => {})
  return output;
}

async function globalFetch(address){
  let output = null;
  await fetch(address)
  .then(response => response.json())
  .then(data => {
    console.log(data);
    output = data;
  })
  .catch(err => {})
  return output;
}

let inputs = [
  {"items":[{"id":1,"name":"klavir","image":"klavir.jpg","price":450,"count":1}],"customer":{"email":"mail@mail.sk","name":"name","street":"street","number":"12","city":"city","postal":"03231"}},
  {"items":[{"id":1,"name":"klavir","image":"klavir.jpg","price":450,"count":1}],"customer":{"email":"bad_mail","name":"name","street":"street","number":"12","city":"city","postal":"03231"}},
  {"items":[{"id":1,"name":"klavir","image":"klavir.jpg","price":450,"count":1}],"customer":{"email":"mail@mail.sk","name":"name","street":"street","number":"bad_number","city":"city","postal":"03231"}},
  {"items":[{"id":1,"name":"klavir","image":"klavir.jpg","price":450,"count":1}],"customer":{"email":"mail@mail.sk","name":"name","street":"street","number":"12","city":"city","postal":"bad_postal"}},
  {"items":[{"id":1,"name":"klavir","image":"klavir.jpg","price":450,"count":1}],"customer":{"email":"mail@mail.sk","name":"","street":"street","number":"12","city":"city","postal":"03231"}},
  {"items":[{"id":0,"name":"klavir","image":"klavir.jpg","price":450,"count":1}],"customer":{"email":"mail@mail.sk","name":"name","street":"street","number":"12","city":"city","postal":"03231"}}
];