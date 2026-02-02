/**
 * Table Search Client-Side
 * Script universal untuk pencarian client-side pada tabel dengan pagination
 * 
 * Cara pakai:
 * 1. Tambahkan input search dengan ID 'searchInput' atau class 'table-search-input'
 * 2. Pastikan tabel punya ID 'tableData' dan tbody dengan ID 'tableBody'
 * 3. Setiap row data harus punya class 'searchable-row' dan attribute 'data-search'
 * 4. Untuk nomor urut dinamis, tambahkan class 'row-number' pada cell nomor
 * 
 * Opsional: set data-base-number pada tbody untuk pagination
 */

(function() {
    'use strict';

    // Fungsi inisialisasi search
    function initTableSearch() {
        // Cari input search (prioritas ID, fallback ke class)
        const searchInput = document.getElementById('searchInput') || 
                           document.querySelector('.table-search-input');
        
        if (!searchInput) {
            console.log('Table search: Input search tidak ditemukan');
            return;
        }

        const tableBody = document.getElementById('tableBody');
        if (!tableBody) {
            console.log('Table search: tbody dengan ID "tableBody" tidak ditemukan');
            return;
        }

        const rows = tableBody.querySelectorAll('.searchable-row');
        if (rows.length === 0) {
            console.log('Table search: Tidak ada row dengan class "searchable-row"');
            return;
        }

        // Ambil base number dari data attribute atau default ke 1
        const baseNumber = parseInt(tableBody.getAttribute('data-base-number')) || 1;

        // Fungsi filter
        function applyFilter(term) {
            const searchTerm = (term || '').toLowerCase().trim();
            let visibleCount = 0;
            let visibleIndex = 0;

            rows.forEach((row) => {
                const searchData = row.getAttribute('data-search');
                
                if (!searchTerm || !searchData || searchData.includes(searchTerm)) {
                    row.style.display = '';
                    
                    // Update nomor urut untuk row yang terlihat
                    const rowNumberCell = row.querySelector('.row-number');
                    if (rowNumberCell) {
                        rowNumberCell.textContent = baseNumber + visibleIndex;
                    }
                    
                    visibleIndex++;
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle pesan "tidak ada hasil"
            const existingNoRow = tableBody.querySelector('#noResultsRow');
            
            if (visibleCount === 0 && searchTerm) {
                if (!existingNoRow) {
                    const colspan = tableBody.closest('table')?.querySelector('thead tr')?.children.length || 10;
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noResultsRow';
                    noResultsRow.innerHTML = `
                        <td colspan="${colspan}" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bx bx-search fs-1 d-block mb-2"></i>
                                Tidak ada hasil untuk pencarian "${searchTerm}"
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(noResultsRow);
                }
            } else if (existingNoRow) {
                existingNoRow.remove();
            }
        }

        // Event listeners
        searchInput.addEventListener('keyup', function() {
            applyFilter(this.value);
        });

        searchInput.addEventListener('change', function() {
            if (this.value === '') {
                applyFilter('');
            }
        });

        console.log('Table search: Inisialisasi berhasil, ' + rows.length + ' baris tersedia');
    }

    // Auto-init saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTableSearch);
    } else {
        initTableSearch();
    }

    // Export untuk manual init jika diperlukan
    window.initTableSearch = initTableSearch;
})();
