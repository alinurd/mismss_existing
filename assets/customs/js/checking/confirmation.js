async function confirmAction(options) {
    return Swal.fire({
        title: options.title || "Yakin?",
        html: options.html || "",
        icon: options.icon || "question",
        showCancelButton: true,
        confirmButtonText: options.confirmText || "Ya",
        cancelButtonText: options.cancelText || "Batal",
        reverseButtons: true
    });
}