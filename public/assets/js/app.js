(function () {
    'use strict';
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    document.querySelectorAll('input[name="cep"]').forEach(function (input) {
        input.addEventListener('input', function () {
            const value = input.value.replace(/\D/g, '').slice(0, 8);
            input.value = value.length > 5 ? value.slice(0, 5) + '-' + value.slice(5) : value;
        });
    });
    document.querySelectorAll('input[name="telefone"]').forEach(function (input) {
        input.addEventListener('input', function () {
            let value = input.value.replace(/\D/g, '').slice(0, 11);
            if (value.length > 10) value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            else if (value.length > 6) value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            else if (value.length > 2) value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
            input.value = value;
        });
    });
})();

