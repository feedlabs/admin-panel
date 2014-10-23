/**
 * @class AP_Page_FeedOverview
 * @extends CM_Page_Abstract
 */
var AP_Page_FeedOverview = CM_Page_Abstract.extend({

  /** @type String */
  _class: 'AP_Page_FeedOverview',

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
