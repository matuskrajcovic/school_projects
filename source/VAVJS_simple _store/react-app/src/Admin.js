//Matus Krajcovic, ID: 103003
import React, {useEffect, useState} from 'react';
import Navigation from './components/Navigation.js'
import Orders from './components/Orders.js'
import AdminAd from './components/AdminAd.js'

function Admin(){
	const [orders, setOrders] = useState([]);
	const [ad, setAd] = useState([]);

	//get all orders from the endpoint
	function getOrders(){
		return fetch('http://localhost:8080/admin')
			.then(data=>data.json())
			.then(data => {if(!data.error) setOrders(data); else{ setOrders([]); alert(data.error)}})
			.catch(err => {setOrders([]); alert(err)});
	}

	//mark an order as completed
	async function markAsDone(id){
		await fetch('http://localhost:8080/markAsDone', {
			method: "POST",
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				"id" : id
			})
		});
		getOrders();
	}

	//get advertisement image end link
	function getAd(){
		fetch('http://localhost:8080/ad')
			.then(data => data.json())
			.then(data => {if(!data.error) setAd(data); else {setAd([]); alert(data.error)}})
			.catch(err => {setAd([]); alert(err)});
	}

	//change advertisement image and link
	async function changeAd(){
		await fetch('http://localhost:8080/changeAd', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				"link" : document.getElementById('link_page').value,
				"image" : document.getElementById('link_image').value
			})
		})
		.catch(err => alert(err));
		getAd();
	}

	useEffect(()=>{
		getOrders();
		getAd();
	},[]);

	return(
		<>
		<Navigation links={[{'link': '/', 'name': 'SPÄŤ'}]} />
		<main>
			<section className="adminPanel">
				<Orders orders={orders} callback={markAsDone}/>
			</section>
			<section className="ads">
				<AdminAd ad={ad} callback={changeAd}/>
			</section>
		</main>
		</>
	);
}

export default Admin;