/* Swal */

function swalDelete(urlDelete) {
    Swal.fire({
        title: "ลบข้อมูล",
        text: "คุณต้องการลบข้อมูลเเถวนี้หรือไม่",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "ตกลง",
        cancelButtonText: "ยกเลิก",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                url: urlDelete,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        $(`#tr-data-${response.beforeId}`).fadeOut("slow");
                    }
                },
            });
        }
    });
}