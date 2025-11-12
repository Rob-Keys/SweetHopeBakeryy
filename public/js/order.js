document.addEventListener('DOMContentLoaded', () => {
	let empty_cart_text = document.getElementById("empty-cart");
	if(!empty_cart_text){
		makeCartVisible();
	}
	const forms = document.querySelectorAll('.add-to-cart-form');
	if(forms.length!=0){
		for (let i=0; i<forms.length; i++){
			const form = forms[i];
			form.addEventListener('submit', function (e) {
				e.preventDefault(); // Prevent actual page reload
				const formData = new FormData(form);

				fetch('/order', {
				  method: 'POST',
				  body: formData
				})
				.then(response => response.json())
				.then(data => {
					let existing = false;
					const items = document.querySelectorAll("#cart-list li");
					for(let i=0; i<items.length; i++){
						let item = items[i];
						let itemExists = false;
						for (let node of item.childNodes) {
							if(node.nodeType===Node.ELEMENT_NODE && node.tagName.toLowerCase() === "p"){
								let product_name_and_qty = node.textContent.trim();
								if(product_name_and_qty.startsWith(data["name"])){
									let qty = product_name_and_qty.match(/\d+/);
									node.textContent = node.textContent.replace(qty[0],parseInt(qty[0])+parseInt(data["quantity"]));
									existing = true;
									itemExists = true;
								}
							}
							else if(node.nodeType===Node.ELEMENT_NODE && itemExists){
								const price_container = node.querySelector(".price");
								const old_price = parseFloat(price_container.textContent.replace(/[^\d.-]/g,""));
								const new_price = old_price + parseFloat(data["price"]);
								price_container.textContent = "$"+new_price.toFixed(2);
							}
						}
				 	}
					if(!existing){
						const newItemHTML =`
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<p>${data["name"]} : (${data["quantity"]})</p>
							<div class="d-flex justify-content-end align-items-center price-container">
								<p class="me-3 price">$${parseFloat(data["price"]).toFixed(2)}</p>
								<span><form method="post" action="/order">
										<input type="hidden" name="removed_name" value="${data["name"]}">
										<button type="submit" name="action" value="remove" style="color: red; background: none; border: none;"><p>X</p></button>
								</form></span>
							</div>
						</li>
						`;
						const cart = document.getElementById("cart-list");
						cart.insertAdjacentHTML('beforeend', newItemHTML);

						const lastElement = cart.lastElementChild;
						if(window.innerWidth < 991){
							lastElement.scrollIntoView({ behavior: 'smooth' });
						} else {
							lastElement.style.animation = 'fadeIn 0.6s ease';
						}

						let empty_cart_text = document.getElementById("empty-cart");
						if(empty_cart_text){
							empty_cart_text.remove();
							makeCartVisible();
						}
					}

					const total_price = document.getElementById("total-price");
					total_price.textContent = "Total: $" + (parseFloat(total_price.textContent.replace(/[^\d.-]/g,"")) + parseFloat(data["price"])).toFixed(2);

					const mobile_total_price = document.getElementById("mobile-total-price");
					mobile_total_price.textContent = "Total: $" + (parseFloat(mobile_total_price.textContent.replace(/[^\d.-]/g,"")) + parseFloat(data["price"])).toFixed(2);

					const message = document.createElement('h5');
					message.textContent = "Added Successfully!";
					message.classList.add('fade-in-out');
					form.appendChild(message);
					requestAnimationFrame(()=>{
						message.classList.add('visible');
					});
					setTimeout(()=>{
						message.classList.remove('visible');
						message.addEventListener("transitionend",()=>{
							form.removeChild(message);
						});
					},1000);
				})
				.catch(error => {
				  console.error('Error:', error);
				});
			});
		}
	}
});

function makeCartVisible(){
	if(window.innerWidth < 991 && !document.querySelector('.cart-container-wrapper').classList.contains("visible")){
		document.querySelector('.cart-container-wrapper').classList.add("visible");
		document.querySelector('.products').classList.add("extra-padding");
		setTimeout(() => {
			const cart = document.getElementById('your-cart');
			if (cart) {
				cart.style.transition = 'opacity 0.5s ease';
				cart.style.opacity = '0';
				
				setTimeout(() => {
					const p = document.createElement('h5');
					p.textContent = cart.textContent;
					p.style.opacity = '0';
					p.style.marginBottom = '0';
					cart.replaceWith(p);
					
					// Fade in the p
					requestAnimationFrame(() => {
						p.style.transition = 'opacity 0.5s ease';
						p.style.opacity = '1';
					});
				}, 500);
			}
		}, 3000);
	}
}