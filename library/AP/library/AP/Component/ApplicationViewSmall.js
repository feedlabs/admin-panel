/**
 * @class AP_Component_ApplicationViewSmall
 * @extends AP_Component_Abstract
 */
var AP_Component_ApplicationViewSmall = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_ApplicationViewSmall',

  events: {
    'click .deleteApplication': function(e) {
      var applicationId = $(e.currentTarget).closest('.application').data('application-id');
      this.deleteApplication(applicationId);
      return false;
    }
  },

  /**
   * @param {Number} applicationId
   */
  deleteApplication: function(applicationId) {
    this.ajax('deleteApplication', {'applicationId': applicationId});
  }
});
