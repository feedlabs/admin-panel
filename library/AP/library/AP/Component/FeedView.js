/**
 * @class AP_Component_FeedView
 * @extends AP_Component_Abstract
 */
var AP_Component_FeedView = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_FeedView',

  events: {
    'click .deleteFeed': function(e) {
      var applicationId = $(e.currentTarget).data('application-id');
      var feedId = $(e.currentTarget).data('feed-id');
      this.deleteFeed(applicationId, feedId);
      return false;
    }
  },

  childrenEvents: {
    'AP_Form_Feed success': function() {
      this.reload();
    }
  },

  /**
   * @param {Number} applicationId
   * @param {Number} feedId
   */
  deleteFeed: function(applicationId, feedId) {
    this.ajax('deleteFeed', {'applicationId': applicationId, 'feedId': feedId});
  }
});
