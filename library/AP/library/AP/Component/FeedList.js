/**
 * @class AP_Component_FeedList
 * @extends AP_Component_Abstract
 */
var AP_Component_FeedList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_FeedList',

  childrenEvents: {
    'AP_Form_Feed success': function() {
      this.reload();
    }
  }
});
