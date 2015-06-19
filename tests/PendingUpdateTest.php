<?php

use App\PendingUpdate;
use App\User;
use Carbon\Carbon;

class PendingUpdateTest extends TestCase
{
    /**
     * Test that the model can be instantiated.
     */
    public function testInstantiation()
    {
        $this->assertInstanceOf('App\PendingUpdate', new PendingUpdate());
    }

    /**
     * Test that the model attribute is (de)serialized on get/set.
     */
    public function testModelDeSerializedOnGetSet()
    {
        $pending = factory(PendingUpdate::class)->make();

        $pending->model = factory(User::class)->create();

        $this->assertEquals('a:2:{i:0;s:8:"App\User";i:1;i:1;}', $pending->getAttributes()['model']);
        $this->assertInstanceOf('App\User', $pending->model);
        $this->assertEquals(1, $pending->model->id);
    }

    /**
     * Test that the update attribute is (de)serialized on get/set.
     */
    public function testUpdateDeSerializedOnGetSet()
    {
        $pending = factory(PendingUpdate::class)->make();

        $pending->update = ['a' => 1, 'b' => 'two'];

        $this->assertEquals('a:2:{s:1:"a";i:1;s:1:"b";s:3:"two";}', $pending->getAttributes()['update']);
        $this->assertEquals(['a' => 1, 'b' => 'two'], $pending->update);
    }

    /**
     * Test that the token and expires_at attributes are generated on create.
     */
    public function testTokenAndExpiresGeneratedOnCreate()
    {
        $pending = factory(PendingUpdate::class)->make();
        $pending->fill([
            'model' => factory(User::class)->create(),
            'update' => ['email' => 'w@x.yz'],
        ]);

        $this->assertNull($pending->token);
        $this->assertNull($pending->expires_at);

        $pending->save();

        $this->assertNotNull($pending->token);
        $this->assertInstanceOf('Carbon\Carbon', $pending->expires_at);
    }

    /**
     * Test a successful execution of a pending update.
     */
    public function testApplySuccess()
    {
        $user = factory(User::class)->create(['email' => 'a@b.cd']);
        $pending = factory(PendingUpdate::class)->make();
        $pending->fill([
            'model' => $user,
            'update' => ['email' => 'w@x.yz'],
        ]);
        $pending->save();

        $this->assertEquals('a@b.cd', $user->email);

        $result = PendingUpdate::apply($pending->token);

        $user = $user->fresh();
        $this->assertEquals('w@x.yz', $user->email);
        $this->assertTrue($result);
        $this->assertNull(PendingUpdate::find($pending->token));
    }

    /**
     * Test a successful execution of a pending update with properties overridden.
     */
    public function testApplyOverrideSuccess()
    {
        $user = factory(User::class)->create(['email' => 'a@b.cd']);
        $pending = factory(PendingUpdate::class)->make();
        $pending->fill([
            'model' => $user,
            'update' => ['email' => 'w@x.yz'],
        ]);
        $pending->save();

        $this->assertEquals('a@b.cd', $user->email);

        $result = PendingUpdate::apply($pending->token, ['email' => 'l@m.no']);

        $user = $user->fresh();
        $this->assertEquals('l@m.no', $user->email);
        $this->assertTrue($result);
        $this->assertNull(PendingUpdate::find($pending->token));
    }

    /**
     * Test a failed execution of a pending update.
     */
    public function testApplyFail()
    {
        $user = factory(User::class)->create(['email' => 'a@b.cd']);
        $pending = factory(PendingUpdate::class)->make();
        $pending->fill([
            'model' => $user,
            'update' => ['email' => 'w@x.yz'],
            'expires_at' => Carbon::now()->subSecond(),
        ]);
        $pending->save();

        $this->assertEquals('a@b.cd', $user->email);

        $result = PendingUpdate::apply($pending->token);

        $user = $user->fresh();
        $this->assertEquals('a@b.cd', $user->email);
        $this->assertFalse($result);
    }
}
