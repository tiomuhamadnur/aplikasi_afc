import './bootstrap';
// import 'laravel-datatables-vite';

import TomSelect from 'tom-select/dist/js/tom-select.complete.js';
import 'tom-select/dist/css/tom-select.default.css';

document.addEventListener('DOMContentLoaded', function () {
    var settings = {};
    document.querySelectorAll('.tom-select-class').forEach(function(el) {
        new TomSelect(el, settings);
    });
});


