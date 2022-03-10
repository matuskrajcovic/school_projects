//Matus Krajcovic, ID: 103003
import React from 'react';

function Ad(props){
	function ad(){
		return props.ad.map((element,index) => {
			return (
				<div className="ad" key={index}>
					<a href={element.link} target="_blank" onClick={e => {props.callback(element.id, element.counter)}}>
						<img width="200px" src={element.image}></img>
					</a><br/>
					<span>Počet kliknutí: {element.counter}</span>
				</div>
			);
		});
	}
	return(
		<section className="ad">
			<h2>Ďakujeme za nákup</h2>
			{ad()}
		</section>
	);
}

export default Ad;