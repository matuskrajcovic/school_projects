//Matus Krajcovic, ID: 103003
import React from 'react';

function Products(props){
	function products(){
		return props.products.map((element,index) => {
			return (
				<article key={index}>
					<h3>{element.name}</h3>
					<img width="200px" src={'/products/' + element.image}></img>
					<span>Cena: {element.price}€</span><br/>
					<input type="number" id={'count_' + element.id} placeholder="počet"></input>
					<button onClick={e=>props.addToCart(element.id)}>PRIDAŤ DO KOŠÍKA</button>
				</article>
			);
		});
	}
	return(
		<section className="products">
			<h2>Produkty</h2>
			{products()}
		</section>
	);
}

export default Products;