$(document).ready(() => {
    let e = localStorage.getItem("theme");
    e || (e = "light"), $(".change-theme").click(() => {
        let e = localStorage.getItem("theme");
        e || (e = "light"), "light" == e ? ($("#theme").attr({
            href: _PAGE_URL + "resources/dist/css/style-dark.min.css"
        }), e = "dark") : ($("#theme").attr({
            href: _PAGE_URL + "resources/dist/css/style.min.css"
        }), e = "light"), localStorage.setItem("theme", e)
    })
});