/**
 * @class AP_App
 * @extends CM_App
 */
var AP_App = CM_App.extend({

  ready: function() {
    CM_App.prototype.ready.call(this);

    this.error.bindType('CM_Exception_AuthRequired', function(msg, type, isPublic) {
      var authRequiredCmp = cm.findView('AP_Component_AuthRequired');
      var hadViewer = cm.viewer !== null;
      if (authRequiredCmp) {
        authRequiredCmp.popOut();
      } else {
        cm.findView().loadComponent('AP_Component_AuthRequired', {hadViewer: hadViewer});
      }
      return false;
    });
  }
});
