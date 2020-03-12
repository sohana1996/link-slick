// ==================
// Page Global Variable
// ==================
var urlListPage = 1;
// ==================
// Functions
// ==================
var incCount = function (trigger) {
    var trigger = $(trigger);
    var container = trigger.closest('.eachUrl');
    var target = container.find('.visitCounter');
    var targetToday = container.find('.visitCounterToday');
    var count = parseInt(target.html());
    var countToday = parseInt(targetToday.html());
    target.html(count + 1);
    targetToday.html(countToday + 1);
};
var openEditModal = function (urlId) {
    var modal = $('#editUrlModal');
    var form = modal.find('form');
    var url = form.find('input[name="url"]');
    var url_android = form.find('input[name="url_android"]');
    var url_ios = form.find('input[name="url_ios"]');
    var title = form.find('input[name="title"]');
    var checkbox = form.find('input[name="checkbox"]');
    var write_script = form.find('textarea[name="write_script"]');
    var cat_id = form.find('select[name="cat_id"]');
    var id = form.find('input[name="id"]');
    url.val('');
    url_android.val('');
    url_ios.val('');
    title.val('');
    write_script.val('');
    cat_id.val('');
    id.val('');
    modal.modal('show');

    $.ajax({
        url: apiUrl + '/get/single/url/' + urlId,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                console.log(res);
                url.val(res.data[0].url);
                url_android.val(res.data[0].url_android);
                url_ios.val(res.data[0].url_ios);
                title.val(res.data[0].title);
                cat_id.val(res.data[0].cat_id);
                id.val(res.data[0].id);
                if(res.data[0].checkbox != null){
                    write_script.val(res.data[0].write_script);
                    write_script.closest('.form-group').show();
                    checkbox.prop('checked', true);
                }
                else{
                    write_script.val('');
                    write_script.closest('.form-group').hide();
                    checkbox.prop('checked', false);
                }
            }
        }
    });
};
var openRemoveModal = function (urlId) {
    var modal = $('#removeUrlModal');
    var form = modal.find('form');
    var id = form.find('input[name="id"]');
    id.val(urlId);
    modal.modal('show');
};
var openCreateModal = function () {
    var modal = $('#createUrlModal');
    var form = modal.find('form');
    form[0].reset();
    modal.modal('show');
};
var changeClickPop = function () {
    var modal = $('#createUrlModal');
    var form = modal.find('form');
    var checkbox = form.find('input[name="checkbox"]:checked').val();
    if(checkbox == 1){
        form.find('textarea[name="write_script"]').closest('.form-group').show();
    }
    else{
        form.find('textarea[name="write_script"]').closest('.form-group').hide();
        form.find('textarea[name="write_script"]').val('');
    }
};
var createShortUrl = function (e) {
    e.preventDefault();
    var modal = $('#createUrlModal');
    var form = $(e.target);
    var createForm = {
        url: form.find('input[name="url"]').val(),
        url_android: form.find('input[name="url_android"]').val(),
        url_ios: form.find('input[name="url_ios"]').val(),
        title: form.find('input[name="title"]').val(),
        checkbox: form.find('input[name="checkbox"]:checked').val(),
        write_script: form.find('textarea[name="write_script"]').val(),
        cat_id: form.find('select[name="cat_id"]').val()
    };
    console.log(createForm);
    $.ajax({
        url: apiUrl + '/create/url',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                form[0].reset();
                modal.modal('hide');
                getShortUrl(1);
                $.notify("New url has been stored successfully", "success");
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
};

var changeClickPopEdit = function () {
    var modal = $('#editUrlModal');
    var form = modal.find('form');
    var checkbox = form.find('input[name="checkbox"]:checked').val();
    if(checkbox == 1){
        form.find('textarea[name="write_script"]').closest('.form-group').show();
    }
    else{
        form.find('textarea[name="write_script"]').closest('.form-group').hide();
        form.find('textarea[name="write_script"]').val('');
    }
};

var updateShortUrl = function (e) {
    e.preventDefault();
    var form = $(e.target);
    var modal = $('#editUrlModal');
    var editForm = {
        id: form.find('input[name="id"]').val(),
        url: form.find('input[name="url"]').val(),
        url_android : form.find('input[name="url_android"]').val(),
        url_ios : form.find('input[name="url_ios"]').val(),
        title: form.find('input[name="title"]').val(),
        checkbox: form.find('input[name="checkbox"]:checked').val(),
        write_script: form.find('textarea[name="write_script"]').val(),
        cat_id: form.find('select[name="cat_id"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    console.log(editForm);

    $.ajax({
        url: apiUrl + '/edit/url',
        method: 'POST',
        data: editForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                // form[0].reset();
                modal.modal('hide');
                getShortUrl(1);
            } else {
                $.notify(res.data.error[0], 'error')
            }
        }
    });
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
                // form[0].reset();
                modal.modal('hide');
                $('#eachUrlPre-' + removeForm.id).remove()
                $.notify(res.msg, 'success');
            }
        }
    });
};
var getShortUrl = function (pageNo) {
    urlListPage = 0;
    $.ajax({
        url: apiUrl + '/get/url/' + pageNo,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                urlListPage = pageNo + 1;
                var html = '';
                $.each(res.data, function (i, v) {
                    var dotUrl = v.url.length > 50 ? '...' : '';
                    html += '<tr class="eachUrl" id="eachUrlPre-' + v.id + '">' +
                        '<td><a href="'+appUrl+'/link/report/'+v.id+'">' + v.title + '</a></td>' +
                        '<td class="text-sm"><a href="'+appUrl+'/link/report/'+v.id+'">' + v.url.substr(0, 50) +dotUrl+ '</a></td>' +
                        '<td class="text-center text-sm">' + v.category + '</td>' +
                        '<td class="text-center">' +
                        '<a class="btn btn-xs" onclick="openEditModal(' + v.id + ')"><i class="fa fa-pencil"></i></a>' +
                        '<a class="btn btn-xs" onclick="openRemoveModal(' + v.id + ')"><i class="fa fa-trash text-danger"></i></a>' +
                        '</td>' +
                        '</tr>';
                });
                if (pageNo === 1) {
                    $('#app-url-preview').html(html);
                } else {
                    $('#app-url-preview').append(html);
                }
            }
        }
    });
};
var getAllcat = function () {
    $.ajax({
        url: apiUrl + '/get/cat/all',
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if(parseInt(res.status) === 2000){
                var target = $('.catDrop');
                var html = '';
                $.each(res.data, function (i, v) {
                    html += '<option value="'+v.id+'">'+v.title+'</option>';
                });
                target.html(html);
            }
        }
    });
};
// ==================
// Document Ready
// ==================
$(function () {
    getAllcat();
    getShortUrl(urlListPage);
    document.onscroll = function () {
        if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
            if (urlListPage !== 0) {
                getShortUrl(urlListPage);
            }
        }
    };
});