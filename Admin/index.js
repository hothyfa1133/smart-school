$(function () {
    $(".full-page").click(function () {
        $(".full-page").fadeOut();
    });

    $(".full-page .content").click(function (e) {
        e.stopPropagation();
    });

    $("small.mark").removeClass('mark');

    $('small.err-msg').addClass('text-danger');

    $("label").addClass("form-label");
});

window.onload = function () {
    document.querySelector('div.loader:first-of-type').style.display = 'none';
}