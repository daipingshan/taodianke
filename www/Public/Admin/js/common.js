$(function() {

    $('#side-menu').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1)
            height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
//layer openiframe

    $(".openifram").click(function() {
        var tit = $("#opendoc").attr("title");
        var hurl = $(this).attr("data-url");
        var w = $("#opendoc").attr("data-w");
        var h = $("#opendoc").attr("data-h")
        if ($(this).attr('title')) {
            tit = $(this).attr('title');
        }
        if ($(this).attr('data-w')) {
            w = $(this).attr('data-w');
        }
        if ($(this).attr('data-h')) {
            h = $(this).attr('data-h');
        }
        layer.open({
            type: 2,
            title: tit,
            shadeClose: true,
            shade: 0.8,
            area: [w + "px", h + "px"],
            content: [hurl] //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
        });
    });



});
// ajax 异步请求操作
function ajax_operation(self) {
    var $this = $(self);
    var href = $this.attr('href');
    var confirm_tip = $this.attr('confirm_tip');

    if ($this.hasClass('disabled')) {
        return false;
    }
    if (confirm_tip) {
        var tip_res = window.confirm(confirm_tip);
        if (!tip_res) {
            return false;
        }
    }

    if (show_message_tip) {
        show_message_tip({});
    }
    var btn_html = $this.html();
    $this.addClass('disabled');
    $this.html('正在处理...');
    $.post(href, {}, function(res) {
        $this.removeClass('disabled');
        $this.html(btn_html);
        if (res.error && res.code && res.code != 0) {
            if (show_message_tip) {
                show_message_tip(res);
            } else {
                window.alert('操作失败');
            }
            return false;
        }
        if (show_message_tip) {
            show_message_tip(res);
        } else {
            window.alert('操作失败');
        }
        window.setTimeout(function() {
            window.location.reload();
        }, 1500);
        return false;
    }, 'json')

    return false;
}

// 校验并提交表单
function ajax_check_submit_from(self) {
    var $this = $(self);
    var $form = $this.parents('form#check-form');
    var $href = $this.attr('check_url');
    var $data = $form.serialize();

    if ($this.hasClass('disabled')) {
        return false;
    }
    show_message_tip({});
    var btn_html = $this.val();
    $this.addClass('disabled');
    $this.val('正在校验...');
    $.post($href, $data, function(res) {
        $this.val(btn_html);
        if (res.code != 0 && res.error) {
            if (show_message_tip) {
                show_message_tip(res);
            } else {
                window.alert('操作失败');
            }
            $this.removeClass('disabled');
        } else {
            $this.val('正在提交...');
            $form.submit();
            return false;
        }
    });
    return false;
}

// 校验并提交表单(弹框)
function ajax_check_submit_from_layer(self) {
    var $this = $(self);
    var $form = $this.parents('form#check-form');
    var $href = $this.attr('check_url');
    var $data = $form.serialize();
    var $form_href = $form.attr('action');

    // ajax 提交表单
    var form_submit = function() {
        $.post($form_href, $data, function(_res) {

            if (show_message_tip) {
                show_message_tip(_res);
            } else {
                parent.window.alert('操作失败');
            }
            if (_res.code != 0 && _res.error) {
                return false;
            }
            window.setTimeout(function() {
                layer.closeAll();
                parent.window.location.reload();
            }, 500);
            return false;


        }, 'json');
        return false;
    }

    if ($this.hasClass('disabled')) {
        return false;
    }
    if (show_message_tip) {
        show_message_tip({});
    }
    var btn_html = $this.val();
    $this.addClass('disabled');
    $this.val('正在校验...');
    $.post($href, $data, function(res) {
        $this.removeClass('disabled');
        $this.val(btn_html);
        console.log(res);
        if (res.code != 0 && res.error) {
            if (show_message_tip) {
                show_message_tip(res);
            } else {
                parent.window.alert('操作失败');
            }
            return false;
        }
        $this.val('正在提交...');
        form_submit();
        return false;
    });
    return false;
}
