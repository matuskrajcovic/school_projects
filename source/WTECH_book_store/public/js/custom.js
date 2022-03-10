$("form").submit(function()
{
	$(this).find('input').each((key, val) => {
		if($(val).val() == '')
			$(val).attr('disabled', 'disabled')
	});

	$(this).find('select').each((key, val) => {
		if($(val).val() == '')
			$(val).attr('disabled', 'disabled')
	});

	//$(this).find(':button[type="submit"]').attr('disabled', 'disabled')

	return true;
});

function update_cart_product(element)
{
	let productId = $(element).next().data('id')
	let newAmount = $(element).val()
	let parent = $(element).parents('.cart-item')

	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: baseUrl + 'update-product',
		type: 'POST',
		contentType: 'application/json',
		data: JSON.stringify({
			product_id: productId,
			amount: newAmount
		}),
		success: (res) => {
		},
		error: (res) => {
			alert("Nastala chyba pri zmene počtu!")
		}
	});
}

function delete_cart_product(element)
{
	let productId = $(element).data('id')
	let parent = $(element).parents('.cart-item')

	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: baseUrl + 'delete-from-cart',
		type: 'POST',
		contentType: 'application/json',
		data: JSON.stringify({
			product_id: productId,
		}),
		success: (res) => {
			$(parent).remove()
			alert("Produkt úspešne odobratý z košíka!")
		},
		error: (res) => {
			alert("Nastala chyba!")
		}
	});
}

function delete_main_image(element)
{
	let productId = $(element).data('id')

	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: baseUrl + 'admin/delete-main-image/' + productId,
		type: 'DELETE',
		success: (res) => {
			$(element).parents('.row').first().remove()
			alert("Fotografia úspešne vymazaná!")
		},
		error: (res) => {
			alert("Nastala chyba!")
		},
	});
}

function delete_other_image(element)
{
	let productId = $(element).data('id')
	let imageId = $(element).data('imageid')
	let imagePath = $(element).data('path')

	$.ajax({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: baseUrl + 'admin/delete-other-image/' + productId,
		type: 'DELETE',
		contentType: 'application/json',
		data: JSON.stringify({
			photo_id: imageId,
			path: imagePath,
		}),
		success: (res) => {
			$(element).parents('li').remove()
			alert("Fotografia úspešne vymazaná!")
		},
		error: (res) => {
			alert("Nastala chyba!")
		},
	});
}
