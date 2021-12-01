
$(document).ready(function() {
    $("._cs").select2({
        minimumInputLength: 1,
        ajax: {
            url: _PAGE_URL + "search-ajax.php?text=a",
            data: function(a) {
                return {
                    name: a
                }
            }
        },
        processResults: function(a) {
            return {
                results: a
            }
        }
    }), $("._cs").on("select2:select", function(a) {
        $("._type").prop("disabled", !0), $("._lt").show();
        var b = $("._cs").val();
        $.ajax({
            url: _PAGE_URL + "types-ajax.php",
            data: {
                user: b
            },
            success: function(a) {
                $("._type").prop("disabled", !1), $("._lt").hide(), $("._type").html(a)
            }
        })
    }), $("._type").change(function() {
        $("._reason").prop("disabled", !0), $("._lr").show();
        var a = $("._type").val();
        $.ajax({
            url: _PAGE_URL + "reasons-ajax.php",
            data: {
                type: a
            },
            success: function(a) {
                $("._reason").prop("disabled", !1), $("._lr").hide(), $("._reason").html(a)
            }
        })
    })
});
$('._acc').click((a) => {
    $('._acc').removeClass('active');
    $(a.target).addClass('active');
    $('input[name="_access"]').val(a.target.name);
});