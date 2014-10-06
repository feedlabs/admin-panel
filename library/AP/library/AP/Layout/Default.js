/**
 * @class AP_Layout_Default
 * @extends CM_Layout_Abstract
 */
var AP_Layout_Default = CM_Layout_Abstract.extend({

  /** @type String */
  _class: 'AP_Layout_Default',

  ready: function() {
    $('[data-toggle="offcanvas"]').click(function() {
      $('.row-offcanvas').toggleClass('active');
    });
  }
});
