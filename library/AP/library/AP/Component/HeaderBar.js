/**
 * @class AP_Component_HeaderBar
 * @extends AP_Component_Abstract
 */
var AP_Component_HeaderBar = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_HeaderBar',

  events: {
    'click .showNavigation': 'showNavigation'
  },

  showNavigation: function() {
    cm.findView('AP_Layout_Default').showNavigation();
  }
});
