/**
 * @class AP_Component_ApplicationView
 * @extends AP_Component_Abstract
 */
var AP_Component_ApplicationView = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_ApplicationView',

  events: {
    'click .deleteApplication': function(e) {
      var applicationId = $(e.currentTarget).data('application-id');
      this.deleteApplication(applicationId);
      return false;
    }
  },

  childrenEvents: {
    'AP_Form_Application success': function() {
      this.reload();
    }
  },

  /**
   * @param {Number} applicationId
   */
  deleteApplication: function(applicationId) {
    this.ajax('deleteApplication', {'applicationId': applicationId});
  }
});
