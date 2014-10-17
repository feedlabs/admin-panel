/**
 * @class AP_Component_Navigation
 * @extends AP_Component_Abstract
 */
var AP_Component_Navigation = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_Navigation',

  /** @type Number */
  coinsBalance: null,

  events: {
    'click .showMenuNonactive': function(e) {
      var $menu = $(e.currentTarget).closest('ul.menu');
      this.showMenuNonactive($menu);
      return false;
    },
    'click .AP_Page_CamShows_Discover a': function() {
      var camShowsAdvertisementLink = this.$('.advertisement .openx-ad').text();
      if (camShowsAdvertisementLink) {
        window.open(camShowsAdvertisementLink, '_blank');
      }
    }
  },

  actions: {
    'AP_Action_User UPDATE_THUMBNAIL': function(action, model, data) {
      this.$('.user-thumb img').attr('src', cm.user.getThumbnailUrl(model));
    },
    'AP_Action_CoinTransaction CREATE': function(action, model, data) {
      this.setCoinsBalance(data['balance']);
    }
  },

  ready: function() {
    this.$('.openx-ad').openx();
    var $thirdMenu = this.$('.menu-sub > li > .menu-sub > li > .menu-sub');
    $thirdMenu.find('> li').prepend($('<a href="javascript:;" class="showMenuNonactive"><span class="icon-arrow-down"></a>'));
    this.hideMenuNonactive($thirdMenu);

    cm.getLayout().on('navigate', function() {
      var $thirdMenu = this.$('.menu-sub > li > .menu-sub > li > .menu-sub');
      this.hideMenuNonactive($thirdMenu);
    }, this);
  },

  /**
   * @param {jQuery} $menu
   */
  hideMenuNonactive: function($menu) {
    $menu.addClass('hideNonactive');
  },

  /**
   * @param {jQuery} $menu
   */
  showMenuNonactive: function($menu) {
    $menu.removeClass('hideNonactive');
  },

  /**
   * @param {Number} count
   */
  setCoinsBalance: function(count) {
    this.$('.coinsBalance').text(count);
  }
});
