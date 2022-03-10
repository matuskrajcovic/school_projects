//Matus Krajcovic, ID: 103003
import React from 'react';
import { useNavigate } from "react-router-dom";

function Navigation(props){
	let navigate = useNavigate();
	function links(){
		return props.links.map((element, index) => {
            return (
				<button key={index} onClick={() => navigate(element.link, {'state': element.state})}>{element.name}</button>
            );
        });
	}
	return(
		<nav>
			{links()}
		</nav>
	);
}

export default Navigation;