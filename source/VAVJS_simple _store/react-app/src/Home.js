//Matus Krajcovic, ID: 103003
import React, {useEffect, useState} from 'react';
import { useNavigate } from "react-router-dom";
import Navigation from './components/Navigation.js'
import Products from './components/Products.js'
import Cart from './components/Cart.js'

function Home(){
	const [products, setProducts] = useState([]);
	const [cart, setCart] = useState([]);
	let navigate = useNavigate();

	//get all products from the endpoint
	function getProducts(){
		return fetch('http://localhost:8080/products')
			.then(data=>data.json())
			.then(data => {if(!data.error) setProducts(data); else {setProducts([]); alert(data.error);}})
			.catch(err => {setProducts([]); alert(err)});
	}

	//add product to cart (no endpoint calls, using useNavigate() to store information)
	function addToCart(id) {
		let count = parseInt(document.getElementById('count_' + id).value);
		if(count > 0){
			let isNew = true;
			cart.forEach(e => {
				if(e.id === id){
					isNew = false;
					e.count += count;
				}
			});
			if(isNew){
				let item = products.filter(e => e.id === id);
				item[0].count = count;
				setCart(cart.concat(item));
			}
			else
				setCart(cart.concat([]));
		}
	}

	//navigate to another page when ordered
	function order() {
		if(cart.length > 0)
			navigate('/order', {'state': {'items': cart}});
	}

	useEffect(()=>{
		getProducts();
	},[]);
	
	return(
		<>
		<Navigation links={[{'link': '/admin','name': 'SOM ADMIN'}]} />
		<main>
			<Products products={products} addToCart={addToCart}/>
			<Cart products={cart}/>
		</main>
		<nav>
			<button onClick={order}>OBJEDNAŤ</button>
		</nav>
		</>
	);
}

export default Home;