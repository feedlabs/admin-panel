/**
 * @class AP_Component_Navigation
 * @extends AP_Component_Abstract
 */
var AP_Component_Navigation = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_Navigation',

  events: {
    'click .processLogout': 'logout'
  },

  logout: function() {
    return this.ajax('logout', {}, {
      success: function() {
        this.trigger('success');
      }
    });
  }
});
