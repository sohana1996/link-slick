// ==================
// Page Global Variable
// ==================
var mediaListPage = 1;
// ==================
// Functions
// ==================
var createMediaModal = function (urlId) {
    var modal = $('#createMediaModal');
    var form = modal.find('form');
    form[0].reset();
    modal.modal('show');
};
var createMedia = function (e) {
    e.preventDefault();
    var modal = $('#createMediaModal');
    var form = $(e.target);
    var createForm = {
        title: form.find('input[name="title"]').val(),
        source_id: form.find('select[name="source_id"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    $.ajax({
        url: apiUrl + '/create/domain',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                form[0].reset();
                getMedia(1);
                $.notify("New medium has been stored successfully", "success");
                modal.modal('hide');
                form[0].reset();
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
};
var openEditModal = function (urlId) {
    var modal = $('#editMediaModal');
    var form = modal.find('form');
    var title = form.find('input[name="title"]');
    var id = form.find('input[name="id"]');
    title.val('');
    id.val('');
    modal.modal('show');

    $.ajax({
        url: apiUrl + '/get/single/domain/' + urlId,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                console.log(res);
                title.val(res.data[0].title);
                id.val(res.data[0].id);
            }
        }
    });
};
var updateMedia = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#editMediaModal');
    var editForm = {
        id: form.find('input[name="id"]').val(),
        title: form.find('input[name="title"]').val()
    };
    $.ajax({
        url: apiUrl + '/edit/domain',
        method: 'POST',
        data: editForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                getMedia(1);
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
};
var openRemoveModal = function (urlId) {
    var modal = $('#removeMediaModal');
    var form = modal.find('form');
    var id = form.find('input[name="id"]');
    id.val(urlId);
    modal.modal('show');
};
var removeMedia = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#removeMediaModal');
    var removeForm = {
        id: form.find('input[name="id"]').val()
    };
    $.ajax({
        url: apiUrl + '/remove/domain',
        method: 'POST',
        data: removeForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                $('#eachMediaPre-' + removeForm.id).remove()
                $.notify(res.msg, 'success');
            }
        }
    });
};
var getMedia = function (pageNo) {
    mediaListPage = 0;
    $.ajax({
        url: apiUrl + '/get/domain/' + pageNo,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                mediaListPage = pageNo + 1;
                var html = '';
                $.each(res.data, function (i, v) {
                    html += '<tr class="eachMedia" id="eachMediaPre-' + v.id + '">' +
                        '<td class="text-center">' + (parseInt((i + 1)) + parseInt(((pageNo - 1) * 10))) + '</td>' +
                        '<td>' + v.title + '</td>' +
                        '<td class="text-center">' +
                        '<a class="btn btn-xs" onclick="openEditModal(' + v.id + ')"><i class="fa fa-pencil"></i></a>' +
                        '<a class="btn btn-xs" onclick="openRemoveModal(' + v.id + ')"><i class="fa fa-trash text-danger"></i></a>' +
                        '</td>' +
                        '</tr>';
                });
                if (pageNo === 1) {
                    $('#app-media-preview').html(html);
                } else {
                    $('#app-media-preview').append(html);
                }
            }
        }
    });
};
// ==================
// Document Ready
// ==================
$(function () {
    getMedia(mediaListPage);
    document.onscroll = function () {
        if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
            if (mediaListPage !== 0) {
                getMedia(mediaListPage);
            }
        }
    };
});
