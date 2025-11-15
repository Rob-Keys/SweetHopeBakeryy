document.addEventListener('DOMContentLoaded', () => {
    // Handle Product Edit/Save button clicks
    const menuRows = document.querySelectorAll('.menu-row[data-item-index]');
    
    menuRows.forEach(row => {
        const editBtn = row.querySelector('.edit-item-btn');
        const saveBtn = row.querySelector('.save-edits-btn');
        const viewModeElements = row.querySelectorAll('.view-mode');
        const editModeElements = row.querySelectorAll('.edit-mode');
        
        // Track images to remove
        let imagesToRemove = [];
        
        // Handle remove image button clicks
        const removeImageBtns = row.querySelectorAll('.remove-image-btn');
        removeImageBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const imageItem = btn.closest('.edit-image-item');
                const imageUrl = imageItem.dataset.imageUrl;
                imagesToRemove.push(imageUrl);
                imageItem.remove();
            });
        });
        
        // Edit button click - switch to edit mode
        editBtn.addEventListener('click', () => {
            // Reset images to remove
            imagesToRemove = [];
            
            // Hide view mode, show edit mode
            viewModeElements.forEach(el => el.style.display = 'none');
            editModeElements.forEach(el => el.style.display = 'block');
            
            // Toggle buttons
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
        });
        
        // Save button click - submit the changes
        saveBtn.addEventListener('click', async () => {
            const itemName = row.querySelector('.edit-itemName').value;
            const originalItemName = row.querySelector('.original-itemName').value;
            const description = row.querySelector('.edit-description').value;
            const prices = row.querySelector('.edit-prices').value;
            const customizations = row.querySelector('.edit-customizations').value;
            const newImagesInput = row.querySelector('.add-images-input');
            
            // Create form data
            const formData = new FormData();
            formData.append('tableName', 'products');
            formData.append('partitionKey', 'itemName');
            formData.append('partitionKeyValue', itemName);
            formData.append('originalPartitionKeyValue', originalItemName);
            formData.append('description', description);
            formData.append('csvPrices', prices);
            formData.append('csvCustomizations', customizations);
            
            // Add images to remove
            formData.append('imagesToRemove', JSON.stringify(imagesToRemove));
            
            // Add new images if any
            if (newImagesInput.files.length > 0) {
                for (let i = 0; i < newImagesInput.files.length; i++) {
                    formData.append('newImages[]', newImagesInput.files[i]);
                }
            }
            
            try {
                // Submit the edit
                const response = await fetch('/customize_edit_item', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    // Reload the page to show updated data
                    window.location.reload();
                } else {
                    alert('Error saving changes. Please try again.');
                    console.error('Save failed:', response.statusText);
                }
            } catch (error) {
                alert('Error saving changes. Please try again.');
                console.error('Save error:', error);
            }
        });
    });

    // Handle Page Section Edit/Save button clicks
    const sectionRows = document.querySelectorAll('.menu-row[data-section-index]');
    
    sectionRows.forEach(row => {
        const editBtn = row.querySelector('.edit-section-btn');
        const saveBtn = row.querySelector('.save-section-btn');
        const viewModeElements = row.querySelectorAll('.view-mode');
        const editModeElements = row.querySelectorAll('.edit-mode');
        const pageName = row.dataset.pageName;
        
        // Track if image should be removed
        let imageToRemove = null;
        
        // Handle remove image button click (only if it exists)
        const removeImageBtn = row.querySelector('.remove-image-btn');
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const imageItem = removeImageBtn.closest('.edit-image-item');
                const imageUrl = imageItem.dataset.imageUrl;
                imageToRemove = imageUrl;
                imageItem.remove();
            });
        }
        
        // Edit button click - switch to edit mode
        editBtn.addEventListener('click', () => {
            // Reset image to remove
            imageToRemove = null;
            
            // Hide view mode, show edit mode
            viewModeElements.forEach(el => el.style.display = 'none');
            editModeElements.forEach(el => el.style.display = 'block');
            
            // Toggle buttons
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
        });
        
        // Save button click - submit the changes
        saveBtn.addEventListener('click', async () => {
            const sectionIndex = row.querySelector('.edit-sectionIndex').value;
            const originalSectionIndex = row.querySelector('.original-sectionIndex').value;
            const headerTextElement = row.querySelector('.edit-headerText');
            const headerText = headerTextElement ? headerTextElement.value : '';
            const bodyText = row.querySelector('.edit-bodyText').value;
            const newImageInput = row.querySelector('.add-images-input');
            
            // Create form data
            const formData = new FormData();
            formData.append('tableName', pageName);
            formData.append('partitionKey', 'sectionIndex');
            formData.append('partitionKeyValue', sectionIndex);
            formData.append('originalPartitionKeyValue', originalSectionIndex);
            if(headerTextElement) {
                formData.append('headerText', headerText);
            }
            formData.append('bodyText', bodyText);
            
            // Add image to remove if marked
            if (imageToRemove) {
                formData.append('imageToRemove', imageToRemove);
            }
            
            // Add new image if selected (only if image input exists)
            if (newImageInput && newImageInput.files.length > 0) {
                formData.append('newImage', newImageInput.files[0]);
            }
            
            try {
                // Submit the edit
                const response = await fetch('/customize_edit_section', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    // Reload the page to show updated data
                    window.location.reload();
                } else {
                    alert('Error saving changes. Please try again.');
                    console.error('Save failed:', response.statusText);
                }
            } catch (error) {
                alert('Error saving changes. Please try again.');
                console.error('Save error:', error);
            }
        });
    });
});
