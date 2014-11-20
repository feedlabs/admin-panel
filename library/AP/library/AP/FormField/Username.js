/**
 * @class AP_FormField_Username
 * @extends CM_FormField_Text
 */
var AP_FormField_Username = CM_FormField_Text.extend({
  _class: 'AP_FormField_Username',

  ready: function() {
    this.enableTriggerChangeOnInput();
    this.on('change', function() {
      this.validate();
    }, this);
  }
});
