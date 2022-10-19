<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\Database\DatabaseTablesUpdated;
use App\Models\Database\DatabaseTable;
use App\Models\Stand\Stand;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use stdClass;

class DatabaseServiceTest extends BaseFunctionalTestCase
{
    private DatabaseService $service;
    private InformationSchemaService $mockInformationSchema;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
        $this->mockInformationSchema = Mockery::mock(InformationSchemaService::class);
        $this->app->instance(InformationSchemaService::class, $this->mockInformationSchema);
        $this->service = $this->app->make(DatabaseService::class);
        DatabaseTable::whereNotIn('name', ['stands', 'controller_positions'])->delete();
        DB::statement('SET @@information_schema_stats_expiry = ' . 1);
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

        $this->mockInformationSchema->shouldReceive('getInformationSchemaTables')
            ->with(['stands', 'controller_positions'])
            ->once()
            ->andReturn(
                DatabaseTable::all()->pluck('name')->map(
                    fn(string $name) => $this->getInformationSchemaTableObject($name, null)
                )
            );

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

        $this->mockInformationSchema->shouldReceive('getInformationSchemaTables')
            ->with(['stands', 'controller_positions'])
            ->once()
            ->andReturn(
                DatabaseTable::all()->pluck('name')->map(
                    fn(string $name) => $this->getInformationSchemaTableObject($name, Carbon::now())
                )
            );

        $this->service->updateTableStatus();
        $table->refresh();
        $this->assertEquals(Carbon::now()->startOfSecond(), $table->updated_at);
    }

    public function testItFiresEventOnTableUpdate()
    {
        $table1 = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table1->updated_at = null;
        $table1->save();

        $table2 = DatabaseTable::where('name', 'controller_positions')->firstOrFail();
        $table2->updated_at = null;
        $table2->save();

        $this->mockInformationSchema->shouldReceive('getInformationSchemaTables')
            ->with(['stands', 'controller_positions'])
            ->once()
            ->andReturn(
                DatabaseTable::all()->pluck('name')->map(
                    fn(string $name) => $this->getInformationSchemaTableObject($name, Carbon::now())
                )
            );

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

        $this->mockInformationSchema->shouldReceive('getInformationSchemaTables')
            ->with(['stands', 'controller_positions'])
            ->once()
            ->andReturn(
                DatabaseTable::all()->pluck('name')->map(
                    fn(string $name) => $this->getInformationSchemaTableObject($name, Carbon::now())
                )
            );

        $this->service->updateTableStatus();
        $table->refresh();
        $this->assertEquals(Carbon::now()->addMinutes(5), $table->updated_at);
    }

    public function testItRunsInTransactions()
    {
        $table = DatabaseTable::where('name', 'stands')->firstOrFail();
        $table->updated_at = null;
        $table->save();

        $this->mockInformationSchema->shouldReceive('getInformationSchemaTables')
            ->with(['stands', 'controller_positions'])
            ->once()
            ->andReturn(
                DatabaseTable::all()->pluck('name')->map(
                    fn(string $name) => $this->getInformationSchemaTableObject($name, Carbon::now())
                )
            );

        DB::transaction(function () {
            $this->service->updateTableStatus();
        });
        $table->refresh();
        $this->assertNotNull($table->updated_at);
    }

    private function getInformationSchemaTableObject(string $tableName, ?Carbon $updateTime): object
    {
        return tap(
            new stdClass(),
            function (stdClass $object) use ($tableName, $updateTime) {
                $object->TABLE_NAME = $tableName;
                $object->UPDATE_TIME = $updateTime;
            }
        );
    }
}
