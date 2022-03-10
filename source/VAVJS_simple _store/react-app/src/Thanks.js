//Matus Krajcovic, ID: 103003
import React, {useEffect, useState} from 'react';
import Navigation from './components/Navigation.js'
import Ad from './components/Ad.js'

function Thanks(){
	const [ad, setAd] = useState([]);

	//get adveritement from the endpoint
	function getAd(){
		fetch('http://localhost:8080/ad')
			.then(data => data.json())
			.then(data => {if(!data.error) setAd(data); else {setAd([]); alert(data.error)}})
			.catch(err => {setAd([]); alert(err)});
	}

	//send click information to teh endpoint (counter)
	function clickAd(id, counter){
		fetch('http://localhost:8080/clickAd', {
			method: "POST",
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				"id" : id,
				"counter" : counter
			})
		})
		.then(data => data.json())
		.then(data => { 
			if(!data.error) {
				ad[0].counter = data.counter;
				setAd(ad.concat([]));
			}
		})
		.catch(err => {alert(err)});
	}

	useEffect(()=>{
		getAd();
	},[]);

	return(
		<>
		<Navigation links={[{'link': '/','name': 'DOMOV'}]} />
		<main>
			<section className="thanksPage">
				<Ad ad={ad} callback={clickAd}/>
			</section>
		</main>
		</>
	);
}

export default Thanks;