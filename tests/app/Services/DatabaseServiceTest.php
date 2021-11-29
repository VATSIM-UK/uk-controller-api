<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Database\DatabaseTablesUpdated;
use App\Models\Database\DatabaseTable;
use App\Models\Stand\Stand;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class DatabaseServiceTest extends BaseFunctionalTestCase
{
    private DatabaseService $service;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        $this->service = $this->app->make(DatabaseService::class);
        DatabaseTable::whereNotIn('name', ['stands', 'controller_positions'])->delete();
        DB::statement('SET @@information_schema_stats_expiry = ' . 100);
        DB::connection('mysql_analyze')->statement('ANALYZE TABLE stands, controller_positions');
    }

    public static function tearDownAfterClass(): void
    {
        // We have to re-seed the database as this test involves committing many things
        shell_exec('php artisan db:seed');
        parent::tearDownAfterClass();
    }

    public function testItUpdatesTableDataWhenNeverUpdated()
    {
        $table = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table->updated_at = null;
        $table->save();

        $this->service->updateTableStatus();
        $table->refresh();
        $this->assertNotNull($table->updated_at);
    }

    public function testItUpdatesTableDataWhenUpdatedSinceLastUpdateTime()
    {
        $table = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table->updated_at = Carbon::now()->subMinutes(5);
        $table->save();
        $stand = Stand::find(1);
        $stand->created_at = Carbon::now()->subHour();
        $stand->save();

        $this->service->updateTableStatus();
        $table->refresh();
        $this->assertNotEquals(Carbon::now()->subMinutes(5), $table->updated_at);
    }

    public function testItFiresEventOnTableUpdate()
    {
        $table1 = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table1->updated_at = null;
        $table1->save();

        $table2 = DatabaseTable::where('name', 'controller_positions')->firstOrFail();
        $table2->updated_at = null;
        $table2->save();

        $this->service->updateTableStatus();

        Event::assertDispatched(function (DatabaseTablesUpdated $event) {
            return $event->getTables()->pluck('name')->toArray() === [
                'stands',
                'controller_positions',
            ];
        });
    }

    public function testItDoesntUpdateTableDateIfUpToDate()
    {
        $table = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table->updated_at = Carbon::now()->addMinutes(5);
        $table->save();
        $stand = Stand::find(1);
        $stand->created_at = Carbon::now()->subHour();
        $stand->save();

        $this->service->updateTableStatus();
        $table->refresh();
        $this->assertEquals(Carbon::now()->addMinutes(5), $table->updated_at);
    }

    public function testItRunsInTransactions()
    {
        $table = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table->updated_at = null;
        $table->save();

        DB::transaction(function () {
            $this->service->updateTableStatus();
        });
        $table->refresh();
        $this->assertNotNull($table->updated_at);
    }
}
