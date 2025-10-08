document.addEventListener('DOMContentLoaded', () => {
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
										<input type="hidden" name="removed_name" value="<?= ${data["name"]}>">
										<button type="submit" name="action" value="remove" style="color: red; background: none; border: none;"><p>X</p></button>
								</form></span>
							</div>
						</li>
						`;
						const cart = document.getElementById("cart-list");
						cart.insertAdjacentHTML('beforeend', newItemHTML);
						let empty_cart_text = document.getElementById("empty-cart");
						if(empty_cart_text){
							empty_cart_text.remove();
						}
					}

					const total_price = document.getElementById("total-price");
					total_price.textContent = "Total: $" + (parseFloat(total_price.textContent.replace(/[^\d.-]/g,"")) + parseFloat(data["price"])).toFixed(2);

					const message = document.createElement('p');
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

	//just for mobile cart button and cart
	const dropdownButton = document.getElementById("dropdownButton");
	if(dropdownButton != null){
		dropdownButton.addEventListener("click", () => {
			document.getElementById("dropdownContent").classList.toggle("show");
			document.querySelector(".total-background").classList.toggle("show");
			document.querySelector(".total-background").addEventListener("click",()=>{
				document.getElementById("dropdownContent").classList.remove("show");
				document.querySelector(".total-background").classList.remove("show");
			})
		});
	};

	const customizations = document.querySelectorAll(".customization");
	if(customizations != null){
		customizations.forEach(customization => {
			customization.addEventListener("click", () => {
				if(customization.textContent != "--"){
					customization.classList.toggle("selectedCustomization");
				}
			});
		});
	};
});
