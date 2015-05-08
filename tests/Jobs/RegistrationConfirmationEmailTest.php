<?php

use App\Jobs\RegistrationConfirmationEmail;
use Illuminate\Support\Facades\Queue;
use League\FactoryMuffin\Facade as FactoryMuffin;

class RegistrationConfirmationEmailTest extends TestCase
{
    /**
     * Test the registration confirmation email.
     */
    public function testHandler()
    {
        $user = FactoryMuffin::create('App\User', [
            'first_name' => 'George',
            'last_name' => 'Washington',
            'email' => 'a@b.cd',
        ]);
        $token = 'a1b2c3d4e5';

        Mail::shouldReceive('send')->once()
            ->with(
                'auth.email.register_confirmation',
                Mockery::on(function ($data) use ($user, $token) {
                    $this->assertInstanceOf('App\User', $data['user']);
                    $this->assertEquals(1, $data['user']->id);
                    $this->assertEquals('a1b2c3d4e5', $data['token']);

                    return true;
                }),
                Mockery::on(function ($closure) {
                    $message = Mockery::mock('Illuminate\Mail\Mailer');
                    $message->shouldReceive('from')->andReturn(Mockery::self());
                    $message->shouldReceive('to')->with('a@b.cd', 'George Washington')->andReturn(Mockery::self());
                    $message->shouldReceive('subject')->andReturn(Mockery::self());
                    $closure($message);

                    return true;
                })
            );

        Queue::push(new RegistrationConfirmationEmail($user, $token));
    }
}
