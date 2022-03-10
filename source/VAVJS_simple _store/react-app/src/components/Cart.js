//Matus Krajcovic, ID: 103003
import React from 'react';

function Cart(props){
	function products(){
		return props.products.map((element,index) => {
			return (
				<article key={index}>
					<h3>{element.name}</h3>
					<span>Cena: {element.price}€</span><br/>
					<span>Počet: {element.count}</span>
				</article>
			);
		});
	}
	return(
		<section className="cart">
			<h2>V košíku</h2>
			{products()}
		</section>
	);
}

export default Cart;