//Matus Krajcovic, ID: 103003
import React from 'react';

function Orders(props){
	function orders(){
		return props.orders.map((element,index) => {
			if(element.done == 1)
				return (
					<tr key={index}>
						<td>{element.name}</td>
						<td>{element.email}</td>
						<td>{element.id}</td>
						<td>VYBAVENÉ</td>
					</tr>
				);
			else
				return (
					<tr key={index}>
						<td>{element.name}</td>
						<td>{element.email}</td>
						<td>{element.id}</td>
						<td><button onClick={e => {props.callback(element.id)}}>VYBAVIŤ</button></td>
					</tr>
				);
		});
	}
	return(
		<section className="orders">
			<h2>Objednávky</h2>
			<table><tbody>
				<tr><th>Meno</th><th>Email</th><th>ID objednávky</th><th>Akcia</th></tr>
				{orders()}
			</tbody></table>
		</section>
	);
}

export default Orders;