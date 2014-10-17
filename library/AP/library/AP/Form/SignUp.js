/**
 * @class AP_Form_SignUp
 * @extends CM_Form_Abstract
 */
var AP_Form_SignUp = CM_Form_Abstract.extend({
  _class: 'AP_Form_SignUp',

  ready: function() {
    var matchSexMap = {1: 2, 2: 1, 4: 4};
    var fieldSex = this.getField('sex');
    var fieldSexMatch = this.getField('match_sex');
    var $fieldSexInput = fieldSex.$('select, input');
    var $fieldMatchSexInput = this.getField('match_sex').$('input');
    var fieldEmail = this.getField('email');

    $fieldSexInput.on('change', function() {
      var matchSex = matchSexMap[fieldSex.getValue()];
      $fieldMatchSexInput.prop('checked', false);
      $fieldMatchSexInput.filter('[value="' + matchSex + '"]').prop('checked', true);
    });

    if (fieldSex.getValue() && !fieldSexMatch.getValue()) {
      $fieldSexInput.trigger('change');
    }

    fieldEmail.on('change', function() {
      fieldEmail.validate();
    });

    this.on('success', function() {
      top.location.assign(cm.getUrl());
    });

    $fieldSexInput.filter('[value="2"]').prop('checked', true);
    $fieldSexInput.trigger('change');
  }
});
