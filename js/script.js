document.addEventListener('DOMContentLoaded', function () {
    // Update order summary in real-time
    if (document.getElementById('order-form')) {
        const form = document.getElementById('order-form');
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        const quantityInputs = form.querySelectorAll('.item-quantity');

        // Function to update summary
        function updateSummary() {
            const summaryItems = document.getElementById('summary-items');
            const totalAmount = document.getElementById('total-amount');
            let summaryHTML = '';
            let total = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const itemId = checkbox.value;
                    const itemName = checkbox.nextElementSibling.querySelector('.item-name').textContent;
                    const itemPrice = parseFloat(checkbox.nextElementSibling.querySelector('.item-price').textContent.replace('$', ''));
                    const quantity = parseInt(form.querySelector(`input[name="quantity[${itemId}]"]`).value);
                    const subtotal = itemPrice * quantity;

                    summaryHTML += `
                        <div class="summary-item">
                            <span>${quantity}x ${itemName}</span>
                            <span>$${subtotal.toFixed(2)}</span>
                        </div>
                    `;

                    total += subtotal;
                }
            });

            summaryItems.innerHTML = summaryHTML || '<p>No items selected</p>';
            totalAmount.textContent = total.toFixed(2);
        }

        // Event listeners
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSummary);
        });

        quantityInputs.forEach(input => {
            input.addEventListener('change', updateSummary);
            input.addEventListener('input', updateSummary);
        });

        // Initial update
        updateSummary();
    }

    // Form validation for numeric inputs
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('keydown', function (e) {
            // Prevent negative values
            if (e.key === '-' || e.key === 'e' || e.key === 'E') {
                e.preventDefault();
            }
        });
    });
});