<?php

class SK_EventHandler_EventBinding_MessageNotificationJobTest extends SKTest_TestCase {

    public function testSendMessage() {
        $sender = SKTest_TH::createUser();
        $recipient = SKTest_TH::createUser();
        $recipient->setOnline(false);
        $recipient->getPreferences()->set('mailbox', 'notify_messages', true);
        $conversation = SKTest_TH::createConversation($sender, $recipient);
        $conversationMessage = SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $recipient,
            'text'         => 'Hello!'));
        $job = $this->getMock('SK_EventHandler_EventBinding_MessageNotificationJob', array('_sendNotification'));
        $job->expects($this->once())->method('_sendNotification')->with($recipient, $conversationMessage);
        $job->run(array('user' => $recipient, 'conversationMessage' => $conversationMessage));
    }

    public function testSendMessage_disabled() {
        $sender = SKTest_TH::createUser();
        $recipient = SKTest_TH::createUser();
        $recipient->setOnline(false);
        $recipient->getPreferences()->set('mailbox', 'notify_messages', false);
        $conversation = SKTest_TH::createConversation($sender, $recipient);
        $conversationMessage = SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $recipient,
            'text'         => 'Hello!'));
        $job = $this->getMock('SK_EventHandler_EventBinding_MessageNotificationJob', array('_sendNotification'));
        $job->expects($this->never())->method('_sendNotification');
        $job->run(array('user' => $recipient, 'conversationMessage' => $conversationMessage));
    }

    public function testSendMessage_online() {
        $sender = SKTest_TH::createUser();
        $recipient = SKTest_TH::createUser();
        $recipient->setOnline(true);
        $recipient->getPreferences()->set('mailbox', 'notify_messages', true);
        $conversation = SKTest_TH::createConversation($sender, $recipient);
        $conversationMessage = SK_Entity_ConversationMessage_Text::createStatic(array(
            'conversation' => $conversation,
            'user'         => $recipient,
            'text'         => 'Hello!'));
        $job = $this->getMock('SK_EventHandler_EventBinding_MessageNotificationJob', array('_sendNotification'));
        $job->expects($this->never())->method('_sendNotification');
        $job->run(array('user' => $recipient, 'conversationMessage' => $conversationMessage));
    }
}
