document.addEventListener('alpine:init', () => {
    Alpine.directive('datepicker', (el, { expression }, { evaluate, effect, cleanup }) => {
        // if (!expression) return;
        expression = expression || '{}';

        // Read dynamic options from the expression
        const options = evaluate(expression) || {};

        // Set defaults if not provided
        const finalOptions = {
            dateFormat: 'dd/mm/yy',
            onSelect: function(dateText) {
                el.value = dateText;
                el.dispatchEvent(new Event('input'));
            },

            ...options
        };

        // Initialize datepicker
        $(el).datepicker(finalOptions);

        // Watch for external changes to the Alpine variable to update the picker
        effect(() => {
            if (!el._x_model) return;

            const modelValue = evaluate(el.getAttribute('x-model'));
            if (modelValue && modelValue !== $(el).val()) {
                $(el).datepicker('setDate', modelValue);
            }
        });

        // Cleanup when the element is removed
        cleanup(() => $(el).datepicker('destroy'));
    });
});

function App() {
    return {
        theme: 'light',
        tab: 'employee',

        init() {
            this.initTheme();
            this.initTabFromUrl();
        },

        initTabFromUrl() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                this.tab = tab;
            }
        },

        initTheme() {
            const theme = localStorage.getItem('theme');
            if (theme) {
                this.theme = theme;
            }
        },

        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', this.theme);
        },
    }
}

function swalAlert({
    icon = "info",
    title = "Are you sure?",
    message = null,
    confirmButtonText = null,
    callback = null
} = {}) {

    return new Promise((resolve) => {

        const options = {
            title: title,
            text: message,
            icon: icon,
            width: '350px',
            heightAuto: false,
            showCancelButton: true,
            cancelButtonText: "Cancel",
        };

        if (confirmButtonText) {
            options.showConfirmButton = true;
            options.confirmButtonText = confirmButtonText;
        } else {
            options.showConfirmButton = false;
            options.timerProgressBar = true;
        }

        Swal.fire(options).then((result) => {
            // Backward compatible callback
            if (typeof callback === "function") {
                callback(result);
            }

            // Resolve the promise for async/await
            resolve(result);
        });
    });
}

