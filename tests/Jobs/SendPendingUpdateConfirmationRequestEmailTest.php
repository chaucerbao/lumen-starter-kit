<?php

use App\Jobs\SendPendingUpdateConfirmationRequestEmail;
use Illuminate\Support\Facades\Queue;
use League\FactoryMuffin\Facade as FactoryMuffin;

class SendPendingUpdateConfirmationRequestEmailTest extends TestCase
{
    /**
     * Test sending a pending update confirmation request e-mail.
     */
    public function testHandle()
    {
        $user = FactoryMuffin::create('App\User', [
            'first_name' => 'George',
            'last_name' => 'Washington',
            'email' => 'a@b.cd',
        ]);
        $token = 'a1b2c3d4e5';
        $subject = 'Confirmation Request';

        Mail::shouldReceive('send')->once()->with(
            'email.confirmation_request',
            Mockery::on(function ($data) {
                $this->assertInstanceOf('App\User', $data['user']);
                $this->assertEquals(1, $data['user']->id);
                $this->assertEquals('a1b2c3d4e5', $data['token']);

                return true;
            }),
            Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mail\Mailer');
                $message->shouldReceive('from')->andReturn(Mockery::self());
                $message->shouldReceive('to')->with('a@b.cd', 'George Washington')->andReturn(Mockery::self());
                $message->shouldReceive('subject')->with('Confirmation Request')->andReturn(Mockery::self());
                $closure($message);

                return true;
            }
        ));

        Queue::push(new SendPendingUpdateConfirmationRequestEmail($user, $token, 'Confirmation Request', 'email.confirmation_request'));
    }
}
