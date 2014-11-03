/**
 * @class AP_Page_Account
 * @extends CM_Page_Abstract
 */
var AP_Page_Account = CM_Page_Abstract.extend({

  /** @type String */
  _class: 'AP_Page_Account',

  events: {
    'click .changePassword': 'changePassword'
  },

  changePassword: function() {
    this.loadComponent('AP_Component_ChangePassword');
  }
});
