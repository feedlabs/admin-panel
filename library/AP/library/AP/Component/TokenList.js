/**
 * @class AP_Component_TokenList
 * @extends AP_Component_Abstract
 */
var AP_Component_TokenList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_TokenList',

  events: {
    'click .deleteToken': function(e) {
      var token = $(e.currentTarget).closest('.token').data('token');
      this.deleteToken(token);
      return false;
    }
  },

  /**
   * @param {Number} token
   */
  deleteToken: function(token) {
    this.ajax('deleteToken', {'token': token});
  }
});
