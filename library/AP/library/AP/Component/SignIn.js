/**
 * @class AP_Component_SignIn
 * @extends AP_Component_Abstract
 */
var AP_Component_SignIn = AP_Component_Abstract.extend({
  _class: 'AP_Component_SignIn',

  ready: function() {
    var handler = this;
    this.$('.forgotPW').click(function() {
      handler.loadComponent('AP_Component_ForgotPassword');
    });
    this.$('.signup').click(function() {
      handler.loadComponent('AP_Component_SignUp');
    });
  }
});
