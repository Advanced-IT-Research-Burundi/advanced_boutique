document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour imprimer la facture
            document.getElementById('printInvoice').addEventListener('click', function() {
                // Masquer tous les éléments non imprimables
                const noPrintElements = document.querySelectorAll('.no-print');
                noPrintElements.forEach(el => el.style.display = 'none');

                // Masquer le reçu POS
                const receiptContainer = document.getElementById('receiptContent');
                if (receiptContainer) {
                    receiptContainer.style.display = 'none';
                }

                // Afficher seulement la facture
                const invoiceContent = document.getElementById('invoiceContent');
                invoiceContent.classList.add('printable-content');

                // Imprimer
                window.print();

                // Restaurer l'affichage normal après impression
                setTimeout(() => {
                    noPrintElements.forEach(el => el.style.display = '');
                    if (receiptContainer) {
                        receiptContainer.classList.add('d-none');
                    }
                    invoiceContent.classList.remove('printable-content');
                }, 1000);
            });

            // Fonction pour imprimer le reçu POS
            document.getElementById('printReceipt').addEventListener('click', function() {
                // Masquer tous les éléments non imprimables
                const noPrintElements = document.querySelectorAll('.no-print');
                noPrintElements.forEach(el => el.style.display = 'none');

                // Masquer la facture
                const invoiceContainer = document.getElementById('invoiceContent');
                if (invoiceContainer) {
                    invoiceContainer.style.display = 'none';
                }

                // Afficher et configurer le reçu POS pour l'impression
                const receiptContainer = document.getElementById('receiptContent');
                receiptContainer.classList.remove('d-none');
                receiptContainer.classList.add('printable-content', 'receipt-print');

                // Imprimer
                window.print();

                // Restaurer l'affichage normal après impression
                setTimeout(() => {
                    noPrintElements.forEach(el => el.style.display = '');
                    if (invoiceContainer) {
                        invoiceContainer.style.display = '';
                    }
                    receiptContainer.classList.add('d-none');
                    receiptContainer.classList.remove('printable-content', 'receipt-print');
                }, 1000);
            });

            // Fonction pour télécharger le PDF
            document.getElementById('downloadPDF').addEventListener('click', function() {
                const saleId = '{{ $sale->id }}'; // Cette variable sera remplacée par Laravel

                // Afficher un indicateur de chargement
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Génération...';
                this.disabled = true;

                // Faire la requête pour générer le PDF
                fetch(`/sales/${saleId}/pdf`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/pdf',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la génération du PDF');
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Créer un lien de téléchargement
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `facture-${saleId.toString().padStart(6, '0')}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la génération du PDF. Veuillez réessayer.');
                })
                .finally(() => {
                    // Restaurer le bouton
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });

            // Gestion des raccourcis clavier
            document.addEventListener('keydown', function(e) {
                // Ctrl + P pour imprimer la facture
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    document.getElementById('printInvoice').click();
                }

                // Ctrl + Shift + P pour imprimer le reçu
                if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                    e.preventDefault();
                    document.getElementById('printReceipt').click();
                }

                // Ctrl + D pour télécharger le PDF
                if (e.ctrlKey && e.key === 'd') {
                    e.preventDefault();
                    document.getElementById('downloadPDF').click();
                }
            });

            // Animation pour les boutons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                });
            });
        });
