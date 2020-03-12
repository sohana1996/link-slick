// ==================
// Page Global Variable
// ==================
var catListPage = 1;
// ==================
// Functions
// ==================
var createCatModal = function (urlId) {
    var modal = $('#CreateCatModal');
    var form = modal.find('form');
    form[0].reset();
    modal.modal('show');
};
var createCat = function (e) {
    e.preventDefault();
    var modal = $('#CreateCatModal');
    var form = $(e.target);
    var createForm = {
        title: form.find('input[name="title"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    $.ajax({
        url: apiUrl + '/create/cat',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                form[0].reset();
                getCat(1);
                $.notify("New category has been stored successfully", "success");
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
    var modal = $('#editCatModal');
    var form = modal.find('form');
    var title = form.find('input[name="title"]');
    var id = form.find('input[name="id"]');
    title.val('');
    id.val('');
    modal.modal('show');

    $.ajax({
        url: apiUrl + '/get/single/cat/' + urlId,
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
var updateCat = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#editCatModal');
    var editForm = {
        id: form.find('input[name="id"]').val(),
        title: form.find('input[name="title"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    $.ajax({
        url: apiUrl + '/edit/cat',
        method: 'POST',
        data: editForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                getCat(1);
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
};
var openRemoveModal = function (urlId) {
    var modal = $('#removeCatModal');
    var form = modal.find('form');
    var id = form.find('input[name="id"]');
    id.val(urlId);
    modal.modal('show');
};
var removeCat = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#removeCatModal');
    var removeForm = {
        id: form.find('input[name="id"]').val()
    };
    $.ajax({
        url: apiUrl + '/remove/cat',
        method: 'POST',
        data: removeForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                $('#eachCatPre-' + removeForm.id).remove()
                $.notify(res.msg, 'success');
            }
        }
    });
};
var getCat = function (pageNo) {
    catListPage = 0;
    $.ajax({
        url: apiUrl + '/get/cat/' + pageNo,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                sourceListPage = pageNo + 1;
                var html = '';
                $.each(res.data, function (i, v) {
                    html += '<tr class="eachCat" id="eachCatPre-' + v.id + '">' +
                        '<td class="text-center">' + (parseInt((i + 1)) + parseInt(((pageNo - 1) * 10))) + '</td>' +
                        '<td>' + v.title + '</td>' +
                        '<td class="text-center">' +
                        '<a class="btn btn-xs" onclick="openEditModal(' + v.id + ')"><i class="fa fa-pencil"></i></a>' +
                        '<a class="btn btn-xs" onclick="openRemoveModal(' + v.id + ')"><i class="fa fa-trash text-danger"></i></a>' +
                        '</td>' +
                        '</tr>';
                });
                if (pageNo === 1) {
                    $('#app-cat-preview').html(html);
                } else {
                    $('#app-cat-preview').append(html);
                }
            }
        }
    });
};
// ==================
// Document Ready
// ==================
$(function () {
    getCat(catListPage);
    document.onscroll = function () {
        if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
            if (catListPage !== 0) {
                getCat(catListPage);
            }
        }
    };
});
