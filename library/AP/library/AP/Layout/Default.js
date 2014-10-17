/**
 * @class AP_Layout_Default
 * @extends CM_Layout_Abstract
 */
var AP_Layout_Default = CM_Layout_Abstract.extend({

  /** @type String */
  _class: 'AP_Layout_Default',

  /** @type {OffCanvas|Null} */
  _offCanvasNavigation: null,

  /** @type {OffCanvas|Null} */
  _offCanvasChat: null,

  /** @type {Boolean} */
  _shareVisible: false,

  events: {
    'click .closeChat': 'hideChat',
    'click .toggleShare': function() {
      this.showShare();
    }
  },

  ready: function() {
    var self = this;

    this._offCanvasNavigation = this.$('#navigation').offCanvas({wrap: '#body-container'}).data('offCanvas');
    this._offCanvasChat = this.$('#chat').offCanvas({wrap: '#body-container'}).data('offCanvas');

    var mediaQuerySmall = {
      match: function() {
        if (self._offCanvasNavigation) {
          self._offCanvasNavigation.enable();
        }
        if (self._offCanvasChat) {
          self._offCanvasChat.enable();
        }
      }
    };

    var mediaQueryMedium = {
      match: function() {
        if (self._offCanvasNavigation) {
          self._offCanvasNavigation.disable();
        }
        if (self._offCanvasChat) {
          self._offCanvasChat.disable();
        }
      }
    };

    var mediaQueryLarge = {
      match: function() {
        if (self._offCanvasNavigation) {
          self._offCanvasNavigation.disable();
        }
        if (self._offCanvasChat) {
          self._offCanvasChat.disable();
        }
      }
    };

    enquire.register('(max-width: 994px)', mediaQuerySmall);
    enquire.register('(min-width: 995px) and (max-width: 1399px)', mediaQueryMedium);
    enquire.register('(min-width: 1400px)', mediaQueryLarge);

    this.on('destruct', function() {
      enquire.unregister('(max-width: 994px)', mediaQuerySmall);
      enquire.unregister('(min-width: 995px) and (max-width: 1399px)', mediaQueryMedium);
      enquire.unregister('(min-width: 1400px)', mediaQueryLarge);
    });

    this.on('navigate', function() {
      this.hideNavigation();
      this.hideChat();
      this.hideShare(true);
    }, this);

    this.bindJquery($(document), 'scroll', _.throttle(function() {
      if ($(document).scrollTop() > 800) {
        this.hideShare();
      }
    }, 200));
  },

  showNavigation: function() {
    if (this._offCanvasNavigation) {
      this._offCanvasNavigation.open('100%', this.$('#navigation').outerHeight(true));
    }
  },

  hideNavigation: function() {
    if (this._offCanvasNavigation) {
      this._offCanvasNavigation.close();
    }
  }
});
