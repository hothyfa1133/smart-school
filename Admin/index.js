$(function () {
    $(".full-page").click(function () {
        $(".full-page").fadeOut();
    });

    $(".full-page .content").click(function (e) {
        e.stopPropagation();
    });

    $("small.err-msg").addClass("form-text text-danger mark");

    $("label").addClass("form-label");
})
