/*
 * Author: CM
 *
 * Dependencies: jquery.transit.js
 */
(function($) {
  var defaults = {
    transitionType: 'transition',
    delay: 500,
    easing: 'snap',
    wrap: '#offCanvas-wrap',
    onopen: null,
    onclose: null
  };

  if (navigator.userAgent.match(/IEMobile\/10\.0/i)) {
    defaults.transitionType = 'animate';
    defaults.easing = 'linear';
  }

  /**
   * @param {jQuery} $element
   * @param {Object} options
   * @constructor
   */
  var OffCanvas = function($element, options) {
    var self = this;
    this.setOptions(options);
    this.active = false;
    this.$element = $element;
    this.disabled = options.disabled;
    if (!this.disabled) {
      this.$element.addClass('offCanvas');
    }
    this.$wrap = $(this.options.wrap).addClass('offCanvas-wrap');
    if (!this.$wrap.length) {
      throw 'Cannot find wrap element with selector `' + this.options.wrap + '`.';
    }
    this.$mask = this.$wrap.find('.offCanvas-mask');
    if (!this.$mask.length) {
      this.$mask = $('<div class="offCanvas-mask" />').prependTo(this.$wrap);
    }
    this.$mask.on('click', function() {
      self.close();
    });
    $(document).on('keydown.offCanvas', function(e) {
      if (e.which == 27) {
        self.close();
      }
    });
  };

  OffCanvas.prototype = {
    options: null,
    active: null,
    disabled: null,
    $element: null,
    $wrap: null,
    $mask: null,
    setOptions: function(options) {
      this.options = $.extend({}, defaults, options || {});
    },
    toggle: function(distance, height) {
      if (this.active) {
        this.close();
      } else {
        this.open(distance, height);
      }
    },
    open: function(distance, height) {
      if (this.active) {
        return;
      }
      if (this.disabled) {
        return;
      }
      var self = this;
      this.active = true;
      $(document).scrollTop(0);
      $('html').addClass('offCanvas-active');
      this.$element.addClass('offCanvas-active');
      this.$wrap.css({'min-height': height});
      this.$element.stop()[this.options.transitionType]({x: distance}, this.options.delay, this.options.easing);
      this.$mask.show().stop()[this.options.transitionType]({opacity: 1}, this.options.delay, this.options.easing);
      if (this.options.onopen) {
        this.options.onopen.call(this);
      }
    },
    /**
     * @param {Boolean} [skipAnimation]
     */
    close: function(skipAnimation) {
      if (!this.active) {
        return;
      }
      if (this.disabled) {
        return;
      }
      var self = this;
      this.active = false;
      var delay = this.options.delay;
      if (skipAnimation) {
        delay = 0;
      }
      this.$element.stop()[this.options.transitionType]({x: 0}, delay, this.options.easing, function() {
        if (!self.active) {
          $('html').removeClass('offCanvas-active');
          self.$element.removeClass('offCanvas-active');
          self.$wrap.css({'min-height': ''});
        }
      });
      this.$mask.stop()[this.options.transitionType]({opacity: 0}, delay, this.options.easing, function() {
        if (!self.active) {
          self.$mask.hide();
        }
      });
      if (this.options.onclose) {
        this.options.onclose.call(this);
      }
    },
    disable: function() {
      this.close(true);
      this.$element.removeClass('offCanvas');
      this.$element.css('transform', 'none');
      this.disabled = true;
    },
    enable: function() {
      this.disabled = false;
      this.$element.addClass('offCanvas');
    },
    isOpen: function() {
      return this.active;
    }
  };

  /**
   * @param {String|Object} action
   * @param {Object} [options]
   * @return {jQuery}
   */
  $.fn.offCanvas = function(action, options) {
    return this.each(function() {
      var $self = $(this);
      var offCanvas = $self.data('offCanvas');
      if (!offCanvas) {
        offCanvas = new OffCanvas($self, action);
        $self.data('offCanvas', offCanvas);
      }

      switch (action) {
        case 'toggle':
          offCanvas.toggle(options.distance, options.height);
          break;
        case 'open':
          offCanvas.open(options.distance, options.height);
          break;
        case 'close':
          offCanvas.close();
          break;
        default:
          break;
      }
    });
  };
})(jQuery);
