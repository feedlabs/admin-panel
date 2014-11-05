/**
 * @class AP_Component_ApplicationList
 * @extends AP_Component_Abstract
 */
var AP_Component_ApplicationList = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_ApplicationList',

  childrenEvents: {
    'AP_Form_Application success': function() {
      this.reload();
    }
  }
});
