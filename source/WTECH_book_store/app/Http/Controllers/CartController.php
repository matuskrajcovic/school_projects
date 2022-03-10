<?php

namespace App\Http\Controllers;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
	private ProductRepositoryInterface $productRepository;

	public function __construct(ProductRepositoryInterface $pri)
	{
		$this->productRepository = $pri;
	}

	public function cart(Request $request)
	{
		if ($request->isMethod('post'))
		{
			if(Auth::check())
			{
				$products = $this->productRepository->getProductsByUserToArr(Auth::user(), 'id');
			}
			else
			{
				$products = json_decode(Cookie::get('products'), true);
				if($products == null)
					$products = [];
				$products = Product::whereIn('id', array_keys($products))->get();
			}

			if($products->count())
			{
				$request->session()->flash('step', 1);
				$request->session()->put('products', $products);

				return redirect()->route('cart-contact');
			}
		}

		if(Auth::check())
		{
			$user = Auth::user();
			$products = $user->user_product;

			return view('cart.cart', [
				'products' => $products,
			]);
		}
		else
		{
			$products = json_decode(Cookie::get('products'), true);
			if($products == null)
				$products = [];

			$p = $this->productRepository->getProductsById(array_keys($products));

			foreach($p as $product)
				$product->amount = $products[$product->id];

			return view('cart.cart', [
				'products' => $p,
			]);
		}

		return view('cart.cart');
	}

	public function cart_contact(Request $request)
	{
		if ($request->isMethod('post'))
		{
			$params = $request->validate([
				'step' => 'integer|min:2|max:5',
				'name' => 'required|string|min:1|max:256',
				'address' => 'required|string|min:1|max:256',
				'city' => 'required|string|min:1|max:256',
				'postal_code' => 'required|string|min:1|max:10',
				'email' => 'required|email:rfc|min:1|max:256',
				'phone' => 'required|string|min:1|max:15',
			]);

			//TODO: return if error

			$request->session()->put('contact-info', $params);
			$request->session()->flash('step', $params['step']);

			return redirect()->route('cart-shipping');
		}

		if(!$request->session()->has('products'))
			abort(404);

		if(Auth::check()){
			$user = User::where('id', Auth::id())->first();
			$address = $user->address;
			return view('cart.contact', [
				'user' => $user,
				'address' => $address
			]);
		}
			
		else
			return view('cart.contact');
	}

	public function cart_shipping(Request $request)
	{
		if ($request->isMethod('post'))
		{
			$params = $request->validate([
				'step' => 'integer|min:2|max:5',
				'shipping-type' => 'required|string',
				'comment' => 'nullable|string|max:256',
			]);

			//TODO: return if error

			$request->session()->put('shipping-info', $params);
			$request->session()->flash('step', $params['step']);

			return redirect()->route('cart-payment');
		}

		if(!$request->session()->has('contact-info'))
			abort(404);

		return view('cart.shipping');
	}

	public function cart_payment(Request $request)
	{
		if ($request->isMethod('post'))
		{
			$params = $request->validate([
				'step' => 'integer|min:2|max:5',
				'payment-method' => 'required|integer|min:1|max:3',
			]);

			//TODO: return if error

			$request->session()->put('payment-info', $params);
			$request->session()->flash('step', $params['step']);

			return redirect()->route('cart-summary');
		}

		if(!$request->session()->has('shipping-info'))
			abort(404);

		return view('cart.payment');
	}

	public function cart_summary(Request $request)
	{
		if ($request->isMethod('post'))
		{
			$params = $request->validate([
				'order-agreement' => 'accepted',
			]);

			$this->order($request);

			Cookie::forget('products');

			$request->session()->remove('products');
			$request->session()->remove('contact-info');
			$request->session()->remove('shipping-info');
			$request->session()->remove('payment-info');
			$request->session()->remove('summary-info');

			return redirect()->route('order-success')->withoutCookie('products');
		}

		if(!$request->session()->has('payment-info'))
			abort(404);

		$products = [];
		$sum = 0;
		$count = 0;
		$user = $request->session()->get('contact-info');

		if(Auth::check())
		{
			$products = Auth::user()->user_product;
			
			foreach($products as $product){
				$sum += $product->pivot->count * $product->price;
				$count += 1;
			}
				
		}
		else
		{
			$prod_ids = json_decode(Cookie::get('products'), true);
			$products = Product::find(array_keys($prod_ids));

			foreach($products as $product)
			{
				$product->amount = $prod_ids[$product->id];
				$sum += $product->amount * $product->price;
				$count += 1;
			}
		}

		$request->session()->put('summary-info', [
			'price' => $sum,
			'count' => $count
		]);

		return view('cart.summary', [
			'user' => $request->session()->get('contact-info'),
			'products' => $products,
			'shipping_info' => $request->session()->get('shipping-info'),
			'shipping' => [
				'post_to_post' => 'Slovenská pošta (na adresu)',
				'post_to_address' => 'Slovenská pošta (na poštu)',
				'courier' => 'Kuriérska služba',
				'to_branch' => 'Osobný odber na pobočke popis',
			],
			'sum' => $sum,
		]);
	}

	public function add_to_cart(Request $request)
	{
		$params = $request->validate([
			'id' => 'required|integer|min:1',
			'amount' => 'required|integer|min:1',
		]);

		// user logged in
		if(Auth::check())
		{
			$user = Auth::user();
			$product = Product::find($params['id']);

			$user->user_product()->attach($product, ['count' => $params['amount']]);
		}
		else
		{
			$cookie = json_decode(Cookie::get('products'), true);
			$product = Product::find($params['id']);

			$cookie[$product->id] = $params['amount'];
			$c = Cookie::forever('products', json_encode($cookie));

			return redirect()->route('cart')->withCookie($c);
		}

		return redirect()->route('cart');
	}

	public function update_cart_product(Request $request)
	{
		$params = $request->validate([
			'product_id' => 'required|integer|min:1',
			'amount' => 'required|integer|min:1',
		]);

		if(Auth::check())
		{
			$row = Auth::user()->user_product()->find($params['product_id']);

			if($row == null)
				return response('', 406);

			$row->pivot->count = (int) $params['amount'];
			$row->pivot->save();

			return response('');
		}
		else
		{
			$product = Product::find($params['product_id']);
			$products = json_decode(Cookie::get('products'), true);

			if($product == null || $products == null)
				return response('', 406);

			if(!isset($products[$params['product_id']]))
				return response('', 406);

			$products[$params['product_id']] = $params['amount'];
			$c = Cookie::forever('products', json_encode($products));

			return response('')->withCookie($c);
		}

		return response('', 406);
	}

	public function delete_from_cart(Request $request)
	{
		$params = $request->validate([
			'product_id' => 'required|integer|min:1',
		]);

		if(Auth::check())
		{
			$product = Product::find($params['product_id']);

			if($product == null)
				return response('', 406);

			Auth::user()->user_product()->detach($product);
			return response('');
		}
		else
		{
			$product = Product::find($params['product_id']);
			$products = json_decode(Cookie::get('products'), true);

			if($product == null || $products == null)
				return response('', 406);

			if(count($products) == 1)
			{
				Cookie::forget('products');
				return response('')->withoutCookie('products');
			}

			if(!isset($products[$params['product_id']]))
				return response('', 406);

			unset($products[$params['product_id']]);
			$c = Cookie::forever('products', json_encode($products));

			return response('')->withCookie($c);
		}

		return response('', 406);
	}

	protected function order(Request $request)
	{
		if (Cache::has('topselling')) {
			Cache::forget('topselling');
		}

		$session_data = $request->session()->all();

		$address = new Address();
		$address->phone = $session_data['contact-info']['phone'];
		$address->address = $session_data['contact-info']['address'];
		$address->city = $session_data['contact-info']['city'];
		$address->postal_code = $session_data['contact-info']['postal_code'];
		$address->save();
		
		$order = new Order();
		if(Auth::check()){
			$order->user()->associate(Auth::user());
			$order->address()->associate($address);
			if(Auth::user()->address != null){
				Auth::user()->address->phone = $session_data['contact-info']['phone'];
				Auth::user()->address->address = $session_data['contact-info']['address'];
				Auth::user()->address->city = $session_data['contact-info']['city'];
				Auth::user()->address->postal_code = $session_data['contact-info']['postal_code'];
				Auth::user()->address->save();
			}
			else{
				$user_address = $address->replicate();
				$user_address->push();
				Auth::user()->address()->associate($user_address);
			}
			Auth::user()->save();
		}
			

		$order->name = $session_data['contact-info']['name'];
		$order->email = $session_data['contact-info']['email'];
		$order->shipping_type = $session_data['shipping-info']['shipping-type'];
		$order->note = $session_data['shipping-info']['comment'];
		$order->count = $session_data['summary-info']['count'];
		$order->price = $session_data['summary-info']['price'];
		
		$order->save();

		if(Auth::check())
			$products = Auth::user()->user_product->pluck('pivot.count', 'id');
		else
			$products = json_decode(Cookie::get('products'), true);

		foreach($products as $id => $amount)
			$order->order_product()->attach($id, ['count' => $amount]);

		if(Auth::check())
			Auth::user()->user_product()->detach();
	}
}

