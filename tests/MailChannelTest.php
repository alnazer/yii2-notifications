<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\notifications\tests;


use tuyakhov\notifications\channels\MailChannel;
use tuyakhov\notifications\messages\MailMessage;
use tuyakhov\notifications\NotifiableInterface;
use tuyakhov\notifications\NotificationInterface;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;

class MailChannelTest extends TestCase
{

    public function testSend() 
    {        
        $recipient = $this->getMockBuilder(NotifiableInterface::class)->getMock();
        $recipient->expects($this->once())
            ->method('routeNotificationFor')
            ->with('mail')
            ->willReturn('test@test.com');
        
        $mailer = $this->getMockBuilder(MailerInterface::class)->getMock();
        $message = $this->getMockBuilder(MessageInterface::class)->getMock();
        $message->method('send');
        $message->expects($this->once())->method('setTo')->with('test@test.com')->willReturnSelf();
        $message->expects($this->once())->method('setFrom')->with('test@admin.com')->willReturnSelf();
        
        $mailer->expects($this->once())
            ->method('compose')
            ->with(['html' => 'testView'], ['name' => 'Test Name'])
            ->willReturn($message);
        
        $channel = \Yii::createObject([
            'class' => MailChannel::className(),
            'mailer' => $mailer,
            'from' => 'test@admin.com'
        ]);
        
        $notification = $this->getMockBuilder(NotificationInterface::class)->getMock();
        $notification->expects($this->once())
            ->method('exportFor')
            ->with('mail')
            ->willReturn(\Yii::createObject([
                'class' => MailMessage::className(),
                'view' => ['html' => 'testView'],
                'viewData' => ['name' => 'Test Name'],
            ]));
        $channel->send($recipient, $notification);
        
    }
}