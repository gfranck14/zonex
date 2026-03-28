
            window.addEventListener('DOMContentLoaded', (event) => {
                if(typeof showToast === 'function') {
                    showToast("{{ session('error') }}", 'error');
                } else {
                    console.log("{{ session('error') }}");
                }
            });
        