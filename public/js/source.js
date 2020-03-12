// ==================
// Page Global Variable
// ==================
var sourceListPage = 1;
// ==================
// Functions
// ==================
var createSourceModal = function (urlId) {
    var modal = $('#CreateSourceModal');
    var form = modal.find('form');
    form[0].reset();
    modal.modal('show');
};
var createSource = function (e) {
    e.preventDefault();
    var modal = $('#CreateSourceModal');
    var form = $(e.target);
    var createForm = {
        title: form.find('input[name="title"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    $.ajax({
        url: apiUrl + '/create/source',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                form[0].reset();
                getSources(1);
                $.notify("New source has been stored successfully", "success");
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
    var modal = $('#editSourceModal');
    var form = modal.find('form');
    var title = form.find('input[name="title"]');
    var id = form.find('input[name="id"]');
    title.val('');
    id.val('');
    modal.modal('show');

    $.ajax({
        url: apiUrl + '/get/single/source/' + urlId,
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
var updateSource = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#editSourceModal');
    var editForm = {
        id: form.find('input[name="id"]').val(),
        title: form.find('input[name="title"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    $.ajax({
        url: apiUrl + '/edit/source',
        method: 'POST',
        data: editForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                getSources(1);
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
};
var openRemoveModal = function (urlId) {
    var modal = $('#removeSourceModal');
    var form = modal.find('form');
    var id = form.find('input[name="id"]');
    id.val(urlId);
    modal.modal('show');
};
var removeSource = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#removeSourceModal');
    var removeForm = {
        id: form.find('input[name="id"]').val()
    };
    $.ajax({
        url: apiUrl + '/remove/source',
        method: 'POST',
        data: removeForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                $('#eachSourcePre-' + removeForm.id).remove()
                $.notify(res.msg, 'success');
            }
        }
    });
};
var getSources = function (pageNo) {
    sourceListPage = 0;
    $.ajax({
        url: apiUrl + '/get/source/' + pageNo,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                sourceListPage = pageNo + 1;
                var html = '';
                $.each(res.data, function (i, v) {
                    html += '<tr class="eachSource" id="eachSourcePre-' + v.id + '">' +
                        '<td class="text-center">' + (parseInt((i + 1)) + parseInt(((pageNo - 1) * 10))) + '</td>' +
                        '<td>' + v.title + '</td>' +
                        '<td class="text-center">' +
                        '<a class="btn btn-xs" onclick="openEditModal(' + v.id + ')"><i class="fa fa-pencil"></i></a>' +
                        '<a class="btn btn-xs" onclick="openRemoveModal(' + v.id + ')"><i class="fa fa-trash text-danger"></i></a>' +
                        '</td>' +
                        '</tr>';
                });
                if (pageNo === 1) {
                    $('#app-source-preview').html(html);
                } else {
                    $('#app-source-preview').append(html);
                }
            }
        }
    });
};
// ==================
// Document Ready
// ==================
$(function () {
    getSources(sourceListPage);
    document.onscroll = function () {
        if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
            if (sourceListPage !== 0) {
                getSources(sourceListPage);
            }
        }
    };
});
