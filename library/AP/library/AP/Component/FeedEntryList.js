/**
 * @class AP_Component_FeedEntryList
 * @extends AP_Component_Abstract
 */
var AP_Component_FeedEntryList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_FeedEntryList',

  events: {
    'click .deleteEntry': function(e) {
      var feedId = $(e.currentTarget).closest('.entry').data('entry-id');
      this.deleteFeed(feedId);
      return false;
    }
  },

  /**
   * @param {Number} entryId
   */
  deleteFeed: function(entryId) {
    this.ajax('deleteEntry', {'entryId': entryId});
  }
});
