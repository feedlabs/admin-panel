/**
 * @class AP_Component_ApplicationFeedList
 * @extends AP_Component_Abstract
 */
var AP_Component_ApplicationFeedList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_ApplicationFeedList',

  events: {
    'click .deleteFeed': function(e) {
      var feedId = $(e.currentTarget).closest('.feed').data('feed-id');
      this.deleteFeed(feedId);
      return false;
    }
  },

  /**
   * @param {Number} feedId
   */
  deleteFeed: function(feedId) {
    this.ajax('deleteFeed', {'feedId': feedId});
  }
});
