function checkConn(){
    if(navigator.onLine){
        return true;
    }

    Swal.fire({
        icon: 'error',
        title: 'Tidak ada koneksi internet',
        text: 'Periksa koneksi Anda lalu coba lagi.'
    });
    return false;
}