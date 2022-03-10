//Matus Krajcovic, ID: 103003
import React from 'react';
import Navigation from './components/Navigation.js'
import { useLocation, useNavigate } from "react-router-dom";
import Cart from './components/Cart.js'

function Order(){
	let navigate = useNavigate();
	const {state} = useLocation();

	//make an order, send all input fields to the endpoint
	function order(){
		fetch('http://localhost:8080/order', {
			method: "POST",
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({"items" : state.items, "customer": {
				"email" : document.getElementById('email').value,
				"name" : document.getElementById('name').value,
				"street" : document.getElementById('street').value,
				"number" : document.getElementById('number').value,
				"city" : document.getElementById('city').value,
				"postal" : document.getElementById('postal').value
			}})
		}).then(response => response.json())
		.then(data => {
			if(!data.error) navigate('/thanks');
			else alert(data.error);
		})
		.catch(err => {alert(err)});
	}

	return(
		<>
		<Navigation links={[{'link': '/','name': 'SPÄŤ'}]} />
		<main>
			<section className="orderInfo">
				<h2>Kontaktné údaje</h2>
				<p>Email vo formáte xx@xx.sk, PSC aj číslo domu musia byť numerické hodnoty.</p>
				<table><tbody>
					<tr><td>Email:</td><td><input type="email" id="email" placeholder="email"></input></td></tr>
					<tr><td>Meno:</td><td><input type="text" id="name" placeholder="meno"></input></td></tr>
					<tr><td>Ulica:</td><td><input type="text" id="street" placeholder="ulica"></input></td></tr>
					<tr><td>Číslo domu:</td><td><input type="text" id="number" placeholder="číslo"></input></td></tr>
					<tr><td>Mesto:</td><td><input type="text" id="city" placeholder="mesto"></input></td></tr>
					<tr><td>PSC:</td><td><input type="text" id="postal" placeholder="PSČ"></input></td></tr>
				</tbody></table>
			</section>
			<Cart products={state.items}/>
		</main>
		<nav>
			<button onClick={order}>POTVRDIŤ</button>
		</nav>
		</>
	);
}

export default Order;