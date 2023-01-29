import Swal from 'sweetalert2';

export function PrintError(error: any): void {
  Swal.fire({
    icon: 'error',
    title: error,
    confirmButtonColor: 'rgb(76, 175, 80)',
    confirmButtonText: 'OK',
    showConfirmButton: true,
  });
}
