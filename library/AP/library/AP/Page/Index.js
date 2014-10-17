/**
 * @class AP_Page_Index
 * @extends CM_Page_Abstract
 */
var AP_Page_Index = CM_Page_Abstract.extend({

  /** @type String */
  _class: 'AP_Page_Index',


  events: {
    'click .showSignUp': 'showSignup'
  },

  showSignup: function() {
    var signup = this.findChild("AP_Component_SignUp");
    signup.popOut();
    return false;
  }

});
