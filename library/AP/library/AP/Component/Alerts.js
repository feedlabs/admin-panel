/**
 * @class AP_Component_Alerts
 * @extends AP_Component_Abstract
 */
var AP_Component_Alerts = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_Alerts',

  actions: {
    'AP_Action_User CONNECT_REQUEST': function(action, model, data) {
      if (model.id == cm.viewer.id) {
        this.addMessage(action.actor, cm.language.get('{$user} wants to be your friend.', {user: cm.user.getUsernameHtml(action.actor, {href: false})}), 'member-add', cm.getUrl(action.actor.path));
      }
    },
    'AP_Action_User CONNECT': function(action, model, data) {
      if (model.id == cm.viewer.id) {
        this.addMessage(action.actor, cm.language.get('{$user} accepted your friend request.', {user: cm.user.getUsernameHtml(action.actor, {href: false})}), 'member-add', cm.getUrl(action.actor.path));
      }
    },
    'AP_Action_Entity_ConversationMessage_Text CREATE': function(action, model, data) {
      this._onActionConversationMessage(action, model, data);
    },
    'AP_Action_Entity_ConversationMessage_Gift CREATE': function(action, model, data) {
      this._onActionConversationMessage(action, model, data);
    },
    'AP_Action_User VIEW': function(action, model, data) {
      if (action.actor.id != cm.viewer.id) {
        this.addMessage(action.actor, cm.language.get('{$user} just viewed your profile.', {user: cm.user.getUsernameHtml(action.actor, {href: false})}), 'eye', cm.getUrl(action.actor.path));
      }
    }
  },

  /**
   * @param {Object} user
   * @param {String} msg
   * @param {String} icon
   * @param {String|Function} link
   */
  addMessage: function(user, msg, icon, link) {
    var $alert = $('<a href="javascript:;" class="alert">' + '<div class="alert-icon icon-' + icon + '"></div>' + '<div class="alert-content">' + '<img class="alert-thumb" src="' + cm.user.getThumbnailUrl(user) + '" />' + '<div class="alert-text">' + msg + '</div></div>' + '</a>');
    if (_.isFunction(link)) {
      var handler = this;
      $alert.on('click', function() {
        link.call(handler);
      });
    } else {
      $alert.attr('href', link);
    }
    var maxAlerts = 3;
    this.$el.children(':lt(' + -(maxAlerts - 1) + ')').stop(true).transition({x: '-20px'}, '400ms', 'snap', function() {
      $(this).remove();
    });

    $alert.stop().appendTo(this.$el).transition({x: '100%'}, '400ms', 'snap', function() {
      $(this).delay(8000).transition({x: '-20px'}, '400ms', 'snap', function() {
        $(this).remove();
      });
    });
  },

  /**
   * @param {Object} action
   * @param {Object} model
   * @param {Object} data
   */
  _onActionConversationMessage: function(action, model, data) {
    if (action.actor.id != cm.viewer.id) {
      var mailbox = cm.findView('AP_Component_Mailbox');
      if (!mailbox || !mailbox.isConversationVisible(model.conversation.id)) {
        this.addMessage(action.actor, cm.language.get('{$user} sent you a message.', {user: cm.user.getUsernameHtml(action.actor, {href: false})}), 'mailbox', cm.getUrl(model.path));
      }
    }
  }
});
