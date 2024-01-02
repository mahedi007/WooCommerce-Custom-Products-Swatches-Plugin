(function ($) {
  $(function () {
    $('.variations_form').each(function (idx, form) {
      var $form = $(form);

      $form.on('click', '.swv-button', function (evt) {
        evt.preventDefault();

        var $btn = $(this);

        if ($btn.hasClass('swv-button-disabled')) {
          return true;
        }

        var $select = $btn.closest('.swv-wrapper').find('select');

        if ($btn.hasClass('swv-button-selected')) {
          $select.val('');
          $btn.removeClass('swv-button-selected');
        } else {
          $select.val($btn.data('val'));
          $btn.parent().find('.swv-button-selected').removeClass('swv-button-selected');
          $btn.addClass('swv-button-selected');
        }

        $select.trigger('change');
      })
      .on( 'click', '.reset_variations', function () {
        $(this).closest('.variations_form').find('.swv-button-selected').removeClass('swv-button-selected');
      } );
    });

    $(document.body).on('click', '.swv-list-btn', function (evt) {
      evt.preventDefault();

      var $btn = $(this);
      var $mainImage = $btn.closest('.product').find('img:first');

      if (!$mainImage.data('origin')) {
        $mainImage.data('origin', {
          src: $mainImage.attr('src'),
          srcset: $mainImage.attr('srcset'),
          sizes: $mainImage.attr('sizes')
        });
      }

      var src, srcset, sizes;

      if ($btn.hasClass('swv-list-btn-selected')) {
        var origin = $mainImage.data('origin');

        src = origin.src;
        srcset = origin.srcset;
        sizes = origin.sizes;

        $btn.removeClass('swv-list-btn-selected');
      } else {
        src = $btn.data('src');
        srcset = $btn.data('srcset');
        sizes = $btn.data('sizes');

        $btn.parent()
          .find('.swv-list-btn-selected')
            .removeClass('swv-list-btn-selected');

        $btn.addClass('swv-list-btn-selected');
      }

      $mainImage
        .attr('src', src)
        .attr('srcset', srcset)
        .attr('sizes', sizes);
    });
  });
})(jQuery);