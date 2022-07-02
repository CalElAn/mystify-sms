import swal from 'sweetalert2';
import '@/../css/swal_styles.css';

export const toast = swal.mixin({
  toast: true,
  position: 'center',
  icon: 'success',
  iconColor: 'white',
  customClass: {
    popup: 'colored-toast',
  },
  showConfirmButton: false,
  timer: 1500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', swal.stopTimer);
    toast.addEventListener('mouseleave', swal.resumeTimer);
  },
});

export function deleteConfirmationDialog(
  callback,
  title = 'Delete?',
  text = "You won't be able to undo this."
) {
  swal
    .fire({
      title: title,
      text: text,
      icon: 'warning',
      showCancelButton: true,
      focusCancel: true,
      confirmButtonColor: '#4568ED',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete',
    })
    .then((result) => {
      if (result.isConfirmed) {
        callback();
      }
    });
}
