import Swal from 'sweetalert2';

// Udostępnij globalnie
window.Swal = Swal;

// Funkcja do wyświetlania powiadomień
window.showNotification = function(type, message, title = null) {
    const icons = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };

    const titles = {
        success: title || 'Sukces!',
        error: title || 'Błąd!',
        warning: title || 'Uwaga!',
        info: title || 'Informacja'
    };

    Swal.fire({
        icon: icons[type] || 'info',
        title: titles[type],
        text: message,
        timer: 10000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: '#1f2937',
        color: '#f3f4f6',
        customClass: {
            popup: 'colored-toast'
        }
    });
};

// Nasłuchuj eventów Livewire
document.addEventListener('livewire:init', () => {
    Livewire.on('notify', (event) => {
        showNotification(event.type, event.message, event.title);
    });
});
