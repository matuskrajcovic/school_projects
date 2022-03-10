//Matus Krajcovic, ID: 103003
import React from 'react';
import ReactDom from 'react-dom';
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Home from './Home.js';
import Order from './Order.js';
import Thanks from './Thanks.js';
import Admin from './Admin.js';

ReactDom.render(
	<BrowserRouter>
		<Routes>
			<Route path="/" element={<Home />} />
			<Route path="order" element={<Order />} />
			<Route path="thanks" element={<Thanks />} />
			<Route path="admin" element={<Admin />} />
		</Routes>
	</BrowserRouter>,
	document.getElementById('root')
);