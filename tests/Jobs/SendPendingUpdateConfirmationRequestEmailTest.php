<?php

use App\Jobs\SendPendingUpdateConfirmationRequestEmail;
use App\User;
use Illuminate\Support\Facades\Queue;

class SendPendingUpdateConfirmationRequestEmailTest extends TestCase
{
    /**
     * Test sending a pending update confirmation request e-mail.
     */
    public function testHandle()
    {
        $user = factory(User::class)->create([
            'first_name' => 'George',
            'last_name' => 'Washington',
            'email' => 'a@b.cd',
        ]);

        Mail::shouldReceive('send')->once()->with(
            'email.confirmation_request',
            Mockery::on(function ($data) {
                $this->assertInstanceOf('App\User', $data['user']);
                $this->assertEquals(1, $data['user']->id);
                $this->assertStringMatchesFormat('%s', $data['token']);

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

        Queue::push(new SendPendingUpdateConfirmationRequestEmail($user, ['email' => 'w@x.yz'], 'Confirmation Request', 'email.confirmation_request'));
    }
}
