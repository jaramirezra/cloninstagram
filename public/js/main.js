var url = window.appUrl || window.location.origin;

window.addEventListener("load", function () {
  $(".btn-like, .btn-dislike").css("cursor", "pointer");

  $(document).on("click", ".btn-like", function (e) {
    e.preventDefault();
    var el = $(this);
    var id = el.data('id');

    $.get(url + '/like/' + id, function (res) {
      if (res.like) {
        el.removeClass('btn-like').addClass('btn-dislike');
        el.css('color', 'red');
        el.find('i').addClass('fas').removeClass('far');

        var num = el.closest('.likes').find('.num_likes');
        num.text(parseInt(num.text(), 10) + 1);
      }
    });
  });

  $(document).on("click", ".btn-dislike", function (e) {
    e.preventDefault();
    var el = $(this);
    var id = el.data('id');

    $.get(url + '/dislike/' + id, function (res) {
      if (res.like) {
        el.removeClass('btn-dislike').addClass('btn-like');
        el.css('color', '#262626');
        el.find('i').addClass('far').removeClass('fas');

        var num = el.closest('.likes').find('.num_likes');
        num.text(Math.max(0, parseInt(num.text(), 10) - 1));
      }
    });
  });

  $('#search').on('submit', function (e) {
    var term = $('#search #search_text').val();
    if (term) {
      $(this).attr('action', url + '/user/' + encodeURIComponent(term));
    }
  });
});