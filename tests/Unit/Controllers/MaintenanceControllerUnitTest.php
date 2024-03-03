<?php

namespace Tests\Unit\Controllers;

use App\Enums\AccountEnum;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MaintenanceControllerUnitTest extends TestCase
{
    public function testResetMustEraseCacheData(): void
    {
        $fakeAccountId = 50;
        $fakeAccountBalance = 10;

        Cache::set(
            AccountEnum::ACCOUNT_CACHE_KEY . $fakeAccountId,
            [
                'id' => $fakeAccountId,
                'balance' => $fakeAccountBalance,
            ]
        );

        $cachedAccount = Cache::get(AccountEnum::ACCOUNT_CACHE_KEY . $fakeAccountId);

        $this->assertEquals($fakeAccountId, $cachedAccount['id']);
        $this->assertEquals($fakeAccountBalance, $cachedAccount['balance']);

        $this->post('reset');

        $cachedAccount = Cache::get(AccountEnum::ACCOUNT_CACHE_KEY . $fakeAccountId);

        $this->assertNull($cachedAccount);
    }
}
