import './bootstrap';
import 'laravel-datatables-vite';
import 'jstree';
import 'pace-js';

import TomSelect from 'tom-select/dist/js/tom-select.complete.js';
import 'tom-select/dist/css/tom-select.default.css';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tom-select-class').forEach(function(el) {
        new TomSelect(el, {
            create: false, // Jika ingin menambahkan item baru, ubah ke true
            sortField: 'text'
        });
    });
});



