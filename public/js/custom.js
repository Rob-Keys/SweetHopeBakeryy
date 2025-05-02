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
					const cart = document.getElementById("cart-list");
					const items = document.querySelectorAll("#cart-list li");
					const total_price = document.getElementById("total-price");
					for(let i=0; i<items.length; i++){
						let item = items[i];
						for (let node of item.childNodes) {
							if(node.nodeType===Node.TEXT_NODE && node.textContent.trim() !== "Added Successfully!"){
								let trimmed_text = node.textContent.trim();
								const price_container = node.parentNode.querySelector(".price-container").querySelector(".price");
								if(trimmed_text.startsWith(data["name"])){
									let qty = trimmed_text.match(/\d+/);
									const newText = node.textContent.replace(qty[0],parseInt(qty[0])+parseInt(data["quantity"]));
									node.textContent = newText;
									existing = true;
									const old_price = parseFloat(price_container.textContent.replace(/[^\d.-]/g,""));
									const new_price = old_price + parseFloat(data["price"]);
									total_price.textContent = "Total: $" + (parseFloat(total_price.textContent.replace(/[^\d.-]/g,"")) + parseFloat(data["price"])).toFixed(2);
									price_container.textContent = "$"+new_price.toFixed(2);
								}
							}
						}
				 	}
					if(!existing){
						const newItemHTML =`
						<li class="list-group-item d-flex justify-content-between align-items-center">
							${data["name"]} : (${data["quantity"]})
							<div class="d-flex justify-content-end align-items-center">
								<span class="me-3">$${parseFloat(data["price"]).toFixed(2)}</span>
								<span><form method="post" action="/order">
										<input type="hidden" name="item_id" value="${data["id"]}">
										<button type="submit" name="action" value="remove" style="color: red; background: none; border: none;">X</button>
								</form></span>
							</div>
						</li>
						`;
						cart.insertAdjacentHTML('beforeend', newItemHTML);
					}
					const message = document.createElement('div');
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

	document.getElementById("dropdownButton").addEventListener("click", () => {
		document.getElementById("dropdownContent").classList.toggle("show");
		document.querySelector(".total-background").classList.toggle("show");
		document.querySelector(".total-background").addEventListener("click",()=>{
			document.getElementById("dropdownContent").classList.remove("show");
			document.querySelector(".total-background").classList.remove("show");
		})
	});
	  
});
