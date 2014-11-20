/*
 * Author: CM
 *
 * Workaround for http://stackoverflow.com/questions/7970389/ios-5-fixed-positioning-and-virtual-keyboard
 */
(function($) {

  if (navigator.userAgent.match(/(iPad|iPhone|iPod)/i)) {
    var inputTypeListWithoutKeyboard = [
      'button', 'reset', 'submit', 'checkbox', 'file', 'image', 'radio'
    ];
    $(document).on('focus', 'textarea, select, input', function(event) {
      var triggersKeyboard = (-1 === inputTypeListWithoutKeyboard.indexOf(this.type));
      if (triggersKeyboard) {
        $('html').addClass('iosKeyboardActive');
      }
    });
    $(document).on('blur', 'textarea, select, input', function(event) {
      $('html').removeClass('iosKeyboardActive');
    });
  }

})(jQuery);
