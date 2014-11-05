/**
 * @class AP_Component_FeedEntryList
 * @extends AP_Component_Abstract
 */
var AP_Component_FeedEntryList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_FeedEntryList',

  events: {
    'click .deleteEntry': function(e) {
      var applicationId = $(e.currentTarget).data('application-id');
      var feedId = $(e.currentTarget).data('feed-id');
      var entryId = $(e.currentTarget).data('entry-id');
      this.deleteEntry(applicationId, feedId, entryId);
      return false;
    }
  },

  /**
   * @param {Number} applicationId
   * @param {Number} feedId
   * @param {Number} entryId
   */
  deleteEntry: function(applicationId, feedId, entryId) {
    this.ajax('deleteEntry', {'applicationId': applicationId, 'feedId': feedId, 'entryId': entryId});
  }
});
