var tableTncBlast = $('#tableTncBlast').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/tncblast/table",
        "type": "GET",
        "dataSrc": function(json){
            return json.data;
        }
    },
    "columnDefs": [{
        "targets": [0, 1],
        "orderable": false,
    }],
    "fixedHeader": false,
    "ordering": true,
    "info": true,
    "autoWidth": true,
    "lengthChange": true,
    "pageLength": pageLength,
    "language": {
        "info": dt_info,
        "infoEmpty": dt_info_empty,
        "infoFiltered": dt_info_filter,
        "search": dt_search_label,
        "searchPlaceholder": dt_search_placeholder,
        "zeroRecords": dt_zero_data,
        "thousands": dt_thousands,
        "processing": dt_processing,
    }
});

$("#tncCheckAll").on("change", function () {
    $(".tncCheckbox").prop("checked", $(this).is(":checked"));
});

$("#saveVersionBtn").on("click", function () {
    let version = $("#tncVersionInput").val().trim();

    if (version === "") {
        Swal.fire("Gagal", "Versi tidak boleh kosong", "error");
        return;
    }

    Swal.fire({
        icon: "warning",
        title: "Ubah Versi TnC?",
        text: "Semua customer akan dianggap belum menerima versi " + version + " sampai blast dikirim.",
        showCancelButton: true,
        confirmButtonText: "Ya, Ubah",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            type: "POST",
            url: location.origin + "/tncblast/version",
            data: { version: version },
            success: function (msg) {
                let json = JSON.parse(msg);
                Swal.fire(json.title, json.text, json.status == 200 ? "success" : "error");
                if (json.status == 200) {
                    tableTncBlast.ajax.reload();
                    refreshPendingCount();
                }
            }
        });
    });
});

function refreshPendingCount() {
    $.ajax({
        type: "GET",
        url: location.origin + "/tncblast/table?draw=1&start=0&length=1",
        success: function (msg) {
            let json = JSON.parse(msg);
            $("#pendingCountLabel").text(json.recordsTotal);
        }
    });
}

function sendBatch(ids) {
    return $.ajax({
        type: "POST",
        url: location.origin + "/tncblast/send",
        data: { ids: ids || [] }
    });
}

function showProgress(processed, total) {
    $("#sendProgressWrap").show();
    $("#sendProgressLabel").show();
    let pct = total > 0 ? Math.round((processed / total) * 100) : 100;
    $("#sendProgressBar").css("width", pct + "%").text(pct + "%");
    $("#sendProgressLabel").text(processed + " terkirim dari " + total + " pending");
}

$("#sendSelectedBtn").on("click", function () {
    let ids = $(".tncCheckbox:checked").map(function () { return $(this).val(); }).get();

    if (ids.length === 0) {
        Swal.fire("Gagal", "Pilih minimal 1 customer terlebih dahulu", "error");
        return;
    }

    Swal.fire({
        icon: "warning",
        title: "Kirim TnC ke " + ids.length + " Customer Terpilih?",
        showCancelButton: true,
        confirmButtonText: "Ya, Kirim",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (!result.isConfirmed) return;

        $("#sendSelectedBtn, #sendAllBtn").attr("disabled", true);
        showProgress(0, ids.length);

        sendBatch(ids).then(function (msg) {
            let json = JSON.parse(msg);
            showProgress(json.sent, ids.length);
            $("#sendSelectedBtn, #sendAllBtn").attr("disabled", false);
            tableTncBlast.ajax.reload();
            refreshPendingCount();
            Swal.fire("Selesai", json.sent + " email terkirim" + (json.failed > 0 ? ", " + json.failed + " gagal" : ""), "success");
        });
    });
});

$("#sendAllBtn").on("click", function () {
    let total = parseInt($("#pendingCountLabel").text()) || 0;

    if (total === 0) {
        Swal.fire("Info", "Tidak ada customer yang pending", "info");
        return;
    }

    Swal.fire({
        icon: "warning",
        title: "Kirim TnC ke Semua " + total + " Customer Pending?",
        text: "Proses ini akan mengirim email satu per satu, jangan tutup tab ini sampai selesai.",
        showCancelButton: true,
        confirmButtonText: "Ya, Kirim Semua",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (!result.isConfirmed) return;

        $("#sendSelectedBtn, #sendAllBtn").attr("disabled", true);

        let processed = 0;
        showProgress(0, total);

        function nextBatch() {
            sendBatch([]).then(function (msg) {
                let json = JSON.parse(msg);
                processed += json.sent;
                showProgress(processed, total);

                if (json.remaining > 0 && json.sent > 0) {
                    nextBatch();
                } else {
                    $("#sendSelectedBtn, #sendAllBtn").attr("disabled", false);
                    tableTncBlast.ajax.reload();
                    refreshPendingCount();
                    Swal.fire("Selesai", "Blast selesai, " + processed + " email terkirim", "success");
                }
            }).catch(function () {
                $("#sendSelectedBtn, #sendAllBtn").attr("disabled", false);
                Swal.fire("Gagal", "Terjadi kesalahan saat mengirim blast", "error");
            });
        }

        nextBatch();
    });
});
