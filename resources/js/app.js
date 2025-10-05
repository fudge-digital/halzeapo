import './bootstrap';
import 'toastr/build/toastr.min.css';
import toastr from 'toastr';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// expose agar bisa dipakai di Blade/script lain
window.toastr = toastr;

// set default options (sesuaikan)
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 5000,
    extendedTimeOut: 1000,
    newestOnTop: true,
    preventDuplicates: true,
};