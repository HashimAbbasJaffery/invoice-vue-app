import Swal from "sweetalert2";

export default function confirm(passed, reject) {
    Swal.fire({
        title: "Do you want to delete it?",
        showCancelButton: true,
        confirmButtonText: "Save",
        denyButtonText: `Don't save`
    }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        passed()
    } else if (result.isDenied) {
        reject()
    }
    });
}
