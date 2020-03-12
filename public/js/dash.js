// ==================
// Page Global Variable
// ==================
var urlListPage = 1;
// ==================
// Functions
// ==================
var getAllUrl= function (id) {
    $.ajax({
        url: apiUrl + '/get/url/all',
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if(parseInt(res.status) === 2000){
                var target = $('.urlDrop');
                var html = '';
                $.each(res.data, function (i, v) {
                    if(id === v.id){
                        html += '<option value="'+v.id+'" selected>'+v.title+'</option>';
                    } else {
                        html += '<option value="'+v.id+'">'+v.title+'</option>';
                    }
                });
                target.html(html);
            }
        }
    });
};
var getAllSources = function () {
    $.ajax({
        url: apiUrl + '/get/source/all',
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if(parseInt(res.status) === 2000){
                var target = $('.sourceDrop');
                var html = '<option value="0">None</option>';
                $.each(res.data, function (i, v) {
                    if(i === 0){
                        getRelatedMedia(v.id);
                    }
                    html += '<option value="'+v.id+'">'+v.title+'</option>';
                });
                target.html(html);
            }
        }
    });
};
var getAllDomain= function () {
    $.ajax({
        url: apiUrl + '/get/domain/all',
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if(parseInt(res.status) === 2000){
                var target = $('.domainDrop');
                var html = '<option value="0">None</option>';
                $.each(res.data, function (i, v) {
                    html += '<option value="'+v.id+'">'+v.title+'</option>';
                });
                target.html(html);
            }
        }
    });
};
var getRelatedMedia = function (source_id) {
    var target = $('.mediaDrop');
    target.html('<option value="0">None</option>');
    $.ajax({
        url: apiUrl + '/get/media/related/'+source_id,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if(parseInt(res.status) === 2000){
                var html = '<option value="0">None</option>';
                $.each(res.data, function (i, v) {
                    html += '<option value="'+v.id+'">'+v.title+'</option>';
                });
                target.html(html);
            }
        }
    });
};
var getRelatedContent = function (id) {
    var target = $('.contentDrop');
    target.html('<option value="0">None</option>');
    $.ajax({
        url: apiUrl + '/get/content/related',
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            if(parseInt(res.status) === 2000){
                var html = '<option value="0">None</option>';
                $.each(res.data, function (i, v) {
                    if(id === v.id){
                        html += '<option value="'+v.id+'" selected>'+v.title+'</option>';
                    } else {
                        html += '<option value="'+v.id+'">'+v.title+'</option>';
                    }
                });
                target.html(html);
            }
        }
    });
};
var changeSourceMedia = function (trigger) {
    var trigger = $(trigger);
    var source_id = parseInt(trigger.val());
    if(source_id > 0){
        getRelatedMedia(source_id);
    } else {
        var target = $('.mediaDrop');
        target.html('<option value="0">None</option>');
        var target = $('.contentDrop');
        target.html('<option value="0">None</option>');
    }
};
var generateShortUrl = function (e) {
    e.preventDefault();
    var modal = $('#generateShortLinkModal');
    var GCL = modal.find('.GCL');
    var GCLF = modal.find('.GCLF');
    var genUri = modal.find('.genUri');
    var popDomainShort = modal.find('#popDomainShort');
    var form = $(e.target);
    var createForm = {
        url_id: form.find('select[name="url_id"]').val(),
        source_id: form.find('select[name="source_id"]').val(),
        media_id: form.find('select[name="media_id"]').val(),
        content_id: form.find('select[name="content_id"]').val(),
        domain_id: form.find('select[name="domain_id"]').val()
    };
    GCL.show();GCLF.hide();
    modal.modal('show');
    $.ajax({
        url: apiUrl + '/generate/url',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                if(res.data.domain_id > 0){
                    var urlShort = res.data.domain+'/'+res.data.short;
                } else {
                    var urlShort = appUrl+'/'+res.data.short;
                }
                popDomainShort.val(res.data.id);
                GCL.hide();GCLF.show();
                var genUri = modal.find('.genUri');
                modal.find('.copyToClip').html('Copy Link').removeClass('btn-success').addClass('btn-default');
                genUri.val(urlShort);
                $.notify("New url has been stored successfully", "success");
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
}
var generateDomainShortUrl = function () {
    var modal = $('#generateShortLinkModal');
    var GCL = modal.find('.GCL');
    var GCLF = modal.find('.GCLF');
    var genUri = modal.find('.genUri');
    var popDomainShort = modal.find('#popDomainShort');
    var form = modal.find('form');
    var param = {
        url_id: form.find('input[name="url_id"]').val(),
        domain_id: form.find('select[name="domain_id"]').val()
    };
    GCL.show();GCLF.hide();
    console.log(param);
    $.ajax({
        url: apiUrl + '/generate/domain/url',
        method: 'POST',
        data: param,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                if(res.data.domain_id > 0){
                    var urlShort = res.data.domain+'/'+res.data.short;
                } else {
                    var urlShort = appUrl+'/'+res.data.short;
                }
                GCL.hide();GCLF.show();
                var genUri = modal.find('.genUri');
                modal.find('.copyToClip').html('Copy Link').removeClass('btn-success').addClass('btn-default');
                genUri.val(urlShort);
                $.notify("New url has been stored successfully", "success");
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
                }
            }
        }
    });
}
var copyToClip = function (e) {
    e.preventDefault();
    var trigger = $(e.target);
    var modal = $('#generateShortLinkModal');
    var copyText = document.getElementById("genUri");
    copyText.select();
    document.execCommand("Copy");
    trigger.html('Copied').removeClass('btn-default').addClass('btn-success');
}
var getShortUrl = function (pageNo) {
    urlListPage = 0;
    $.ajax({
        url: apiUrl + '/get/url/' + pageNo,
        method: 'GET',
        success: function (response) {
            var res = JSON.parse(response);
            console.log(res);
            if (res.status === 2000) {
                urlListPage = pageNo + 1;
                var html = '';
                $.each(res.data, function (i, v) {
                    var dotUrl = v.url.length > 50 ? '...' : '';
                    html += '<tr class="eachUrl" id="eachUrlPre-' + v.id + '">' +
                        '<td><a href="'+appUrl+'/link/report/'+v.id+'">' + v.title + '</a></td>' +
                        '<td class="text-sm"><a href="'+appUrl+'/link/report/'+v.id+'">' + v.url.substr(0, 50) +dotUrl+ '</a></td>' +
                        '<td class="text-center visitCounterToday">' + v.report[6] + '</td>' +
                        '<td class="text-center">' + v.report[5] + '</td>' +
                        '<td class="text-center visitCounter">' + v.visit + '</td>' +
                        '<td class="text-center noPadding" style="width: 150px"><div id="chart_div-'+v.id+'" class="shortGraph"></div></td>' +
                        '</tr>';
                });
                if (pageNo === 1) {
                    $('#app-url-preview').html(html);
                } else {
                    $('#app-url-preview').append(html);
                }

                $.getScript( "https://www.gstatic.com/charts/loader.js", function( data, textStatus, jqxhr ) {
                    $.each(res.data, function (i, v) {
                        google.charts.load('current', {'packages':['corechart']});
                        google.charts.setOnLoadCallback(function(){
                            var data = google.visualization.arrayToDataTable([
                                ['Day', 'visit'],
                                ['',  v.report[0]],
                                ['',  v.report[1]],
                                ['',  v.report[2]],
                                ['',  v.report[3]],
                                ['',  v.report[4]],
                                ['',  v.report[5]],
                                ['',  v.report[6]]
                            ]);
                            var options = {
                                hAxis: {title: 'Last 7 Days',  titleTextStyle: {color: '#333'}},
                                vAxis: {minValue: 0}
                            };
                            var chart = new google.visualization.AreaChart(document.querySelector('#chart_div-'+v.id));
                            chart.draw(data, options);
                        });
                    });
                });
            }
        }
    });
};
// ===================
// Quick Add
// ===================
var createContentModal = function () {
    var modal = $('#createContentModal');
    var form = modal.find('form');
    form[0].reset();
    modal.modal('show');
};
var createQuickContent = function (e) {
    e.preventDefault();
    var modal = $('#createContentModal');
    var form = $(e.target);
    var createForm = {
        title: form.find('input[name="title"]').val(),
        _token: form.find('input[name="_token"]').val()
    };
    $.ajax({
        url: apiUrl + '/create/content',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                form[0].reset();
                getRelatedContent(res.data.id);
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
    $.ajax({
        url: apiUrl + '/create/url',
        method: 'POST',
        data: createForm,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status === 2000) {
                form[0].reset();
                modal.modal('hide');
                getAllUrl(res.data.id);
                $.notify("New url has been stored successfully", "success");
            } else {
                if (res.data.error !== undefined) {
                    $.notify(res.data.error[0], "error");
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
    getAllUrl(0);
    getAllcat();
    getRelatedContent();
    getAllSources();
    getAllDomain();
    getShortUrl(urlListPage);
    document.onscroll = function () {
        if ($(window).scrollTop() + $(window).height() + 200 > $(document).height()) {
            if (urlListPage !== 0) {
                getShortUrl(urlListPage);
            }
        }
    };
});
