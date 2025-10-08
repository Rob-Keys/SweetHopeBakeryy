document.addEventListener('DOMContentLoaded', () => {
    const bodyInput = document.getElementById('body-input');
    const bodyPreview = document.getElementById('body-preview');
    bodyInput.addEventListener('input', () => {
        bodyPreview.innerHTML = bodyInput.value;
    })
});