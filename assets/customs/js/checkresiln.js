$("input[name='resiln[]']").each(function () {
    $(this).rules("add", {
        required: true,
        remote: {
            url: location.origin + "/shiptrip/check/foreigntrackid",
            type: "GET",
            data: {
                id: function () {
                    return $(this).val();
                },
                edit: function () {
                    return $(this).data("edit-form");
                }
            }
        },
        messages: {
            remote: "Resi LN Duplikat"
        }
    });
});
