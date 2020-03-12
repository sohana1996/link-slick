// ==================
// Functions
// ==================
var openRemoveModal = function (urlId) {
    var modal = $('#removeUrlModal');
    var form = modal.find('form');
    var id = form.find('input[name="id"]');
    id.val(urlId);
    modal.modal('show');
};
var removeShortUrl = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#removeUrlModal');
    var removeForm = {
        id: form.find('input[name="id"]').val()
    };
    $.ajax({
        url: apiUrl + '/remove/url',
        method: 'POST',
        data: removeForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                location.reload();
            }
        }
    });
};