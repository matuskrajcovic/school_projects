$('.product-images div button').click((event) => {
	event.preventDefault()
	delete_main_image(event.target)
})

$('.product-images li button').click((event) => {
	event.preventDefault()
	delete_other_image(event.target)
})
