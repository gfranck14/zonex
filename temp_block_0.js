
            window.addEventListener('DOMContentLoaded', (event) => {
                if(typeof showToast === 'function') {
                    showToast("{{ session('success') }}", 'success');
                } else {
                    console.log("{{ session('success') }}");
                }
            });
        