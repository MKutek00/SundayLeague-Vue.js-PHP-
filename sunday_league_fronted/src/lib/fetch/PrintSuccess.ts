import Swal from 'sweetalert2';

export function PrintSuccess(msg = 'Zapisano pomyślnie'): void {
  Swal.fire({
    icon: 'success',
    title: msg,
    timer: 2000,
    showConfirmButton: false,
  });
}
