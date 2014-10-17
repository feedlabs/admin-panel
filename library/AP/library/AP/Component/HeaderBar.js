/**
 * @class AP_Component_HeaderBar
 * @extends AP_Component_Abstract
 */
var AP_Component_HeaderBar = AP_Component_Abstract.extend({

  /** @type String */
  _class: 'AP_Component_HeaderBar',

  /** @type Number */
  friendRequestCount: null,

  /** @type Number */
  conversationCount: null,

  /** @type AP_Form_SearchContent|Null */
  _searchForm: null,

  events: {
    'click .showNavigation': 'showNavigation'
  },

  childrenEvents: {
    'CM_FormField_Text blur': function() {
      this._expandSearch(false);
    }
  },

  actions: {
    'AP_Action_User CONNECT_REQUEST, CONNECT_UNREQUEST': function(action, model, data) {
      this.setFriendRequestCount(data.friendRequestCount);
    },
    'AP_Action_Entity_ConversationMessage_Text CREATE': function(action, model, data) {
      if (!_.isUndefined(data) && !_.isUndefined(data.conversationUnreadCount)) {
        this._setConversationUnread(model.conversation.id, data.conversationUnreadCount);
      }
    },
    'AP_Action_Entity_ConversationMessage_Text VIEW': function(action, model, data) {
      if (!_.isUndefined(data) && !_.isUndefined(data.conversationUnreadCount)) {
        this.setConversationCount(data.conversationUnreadCount);
      }
    },
    'AP_Action_Entity_ConversationMessage_Gift CREATE': function(action, model, data) {
      if (!_.isUndefined(data) && !_.isUndefined(data.conversationUnreadCount)) {
        this._setConversationUnread(model.conversation.id, data.conversationUnreadCount);
      }
    },
    'AP_Action_Entity_ConversationMessage_Gift VIEW': function(action, model, data) {
      if (!_.isUndefined(data) && !_.isUndefined(data.conversationUnreadCount)) {
        this.setConversationCount(data.conversationUnreadCount);
      }
    },
    'AP_Action_Entity_Conversation DELETE, BLOCK, UNBLOCK': function(action, model, data) {
      if (!_.isUndefined(data) && !_.isUndefined(data.conversationUnreadCount)) {
        this.setConversationCount(data.conversationUnreadCount);
      }
    }
  },

  ready: function() {
    this.setConversationCount(this.conversationCount);
    this.setFriendRequestCount(this.friendRequestCount);

    this.bindJquery($(document), 'scroll', _.throttle(function() {
      this.$('.toggleShare.active').toggleClass('hideTriangle', $(document).scrollTop() > 20);
    }, 200));
  },

  setFriendRequestCount: function(count) {
    this.$('.indication.friendRequest').toggleClass('empty', count == 0).find('.counter').text(count);
  },

  setConversationCount: function(count) {
    this.$('.indication.conversation').toggleClass('empty', count == 0).find('.counter').text(count);
  },

  showNavigation: function() {
    cm.findView('AP_Layout_Default').showNavigation();
  },

  /**
   * @param {Boolean} state
   */
  _expandSearch: function(state) {
    this.$('.bar').toggleClass('search-expand', state);
  },

  /**
   * @param {Number} conversationId
   * @param {Number} conversationUnreadCount
   */
  _setConversationUnread: function(conversationId, conversationUnreadCount) {
    var mailbox = cm.findView('AP_Component_Mailbox');
    if (mailbox && mailbox.isConversationVisible(conversationId)) {
      mailbox.getConversationView().setRead();
    } else {
      this.setConversationCount(conversationUnreadCount);
    }
  }
});
