/**
 * Created by Ramon on 30-11-2017.
 */
setTimeout(function () {
    $('.alert').fadeOut('fast');
}, 3000); // <-- tijd in milliseconden



$('tr[data-href]').on("click", function () {
    document.location = $(this).data('href');
});