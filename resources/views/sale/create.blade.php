@extends('layouts.app')

@section('title', 'Nouvelle Vente')

@section('content')
    <livewire:sales.sale-create />
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show low stock modal if there are alerts
    @if($products->where('stocks', function($query) { $query->where('quantity', '<=', 'alert_quantity'); })->count() > 0)
    const lowStockModal = new bootstrap.Modal(document.getElementById('lowStockModal'));
    // Show after a delay to ensure page is loaded
    setTimeout(() => {
        lowStockModal.show();
    }, 1000);
    @endif

    // Quick add client functionality
    const quickClientForm = document.getElementById('quickClientForm');
    const quickAddModal = document.getElementById('quickAddClientModal');
    const clientSelect = document.getElementById('client_id');



    // Auto-save draft functionality (optional)
    let draftTimer;
    const formInputs = document.querySelectorAll('#saleForm input, #saleForm select');

    formInputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(draftTimer);
            draftTimer = setTimeout(saveDraft, 2000); // Save draft after 2 seconds of inactivity
        });
    });

    function saveDraft() {
        const formData = new FormData(document.getElementById('saleForm'));
        const draftData = {};

        for (let [key, value] of formData.entries()) {
            draftData[key] = value;
        }

        // Save to sessionStorage (since localStorage is not available)
        try {
            sessionStorage.setItem('sale_draft', JSON.stringify(draftData));
        } catch (e) {
            // Ignore if sessionStorage is not available
        }
    }

    // Load draft on page load
    function loadDraft() {
        try {
            const draftData = sessionStorage.getItem('sale_draft');
            if (draftData) {
                const data = JSON.parse(draftData);

                // Only load draft if form is empty
                const clientSelect = document.getElementById('client_id');
                if (!clientSelect.value && data.client_id) {
                    Object.keys(data).forEach(key => {
                        const input = document.querySelector(`[name="${key}"]`);
                        if (input && input.value === '') {
                            input.value = data[key];
                        }
                    });

                    // Show draft loaded message
                    showAlert('info', 'Brouillon restauré automatiquement.');
                }
            }
        } catch (e) {
            // Ignore errors
        }
    }

    // Load draft after a short delay
    setTimeout(loadDraft, 500);

    // Clear draft when form is successfully submitted
    document.getElementById('saleForm').addEventListener('submit', function() {
        try {
            sessionStorage.removeItem('sale_draft');
        } catch (e) {
            // Ignore errors
        }
    });
});
</script>
@endpush
