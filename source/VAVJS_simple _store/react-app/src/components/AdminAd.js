//Matus Krajcovic, ID: 103003
import React from 'react';

function AdminAd(props){
	//const [image, setImage] = useState(null);
	function ad(){
		return props.ad.map((element,index) => {
			return (
				<div className="ad" key={index}>
					<span>Link: {element.link}</span><br/>
					<img width="200px" src={element.image}></img><br/>
					<input type="hidden" id="imageId" value={element.id}></input>
					<span>Počet kliknutí: {element.counter}</span>
				</div>
			);
		});
	}
	
	return(
		<section className="ad">
			<h2>Reklama</h2>
			{ad()}
			<h2>Zmena reklamy</h2>
			<table><tbody>
				<tr><td>Stránka (link):</td><td><input type="text" id="link_page" placeholder="link"></input></td></tr>
				<tr><td>Obrázok (link):</td><td><input type="text" id="link_image" placeholder="link"></input></td></tr>
			</tbody></table>
			<button onClick={props.callback}>ZMENIŤ</button>
		</section>
	);
}

export default AdminAd;