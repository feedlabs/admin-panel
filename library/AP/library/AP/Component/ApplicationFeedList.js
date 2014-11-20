/**
 * @class AP_Component_ApplicationFeedList
 * @extends AP_Component_Abstract
 */
var AP_Component_ApplicationFeedList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_ApplicationFeedList',

  events: {
    'click .deleteFeed': function(e) {
      var applicationId = $(e.currentTarget).data('application-id');
      var feedId = $(e.currentTarget).data('feed-id');
      this.deleteFeed(applicationId, feedId);
      return false;
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
