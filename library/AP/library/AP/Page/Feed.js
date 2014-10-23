/**
 * @class AP_Page_Feed
 * @extends CM_Page_Abstract
 */
var AP_Page_Feed = CM_Page_Abstract.extend({

  /** @type String */
  _class: 'AP_Page_Feed',

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
