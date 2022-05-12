<?php

use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class RenumberHeathrowStands extends Migration
{
    private const STANDS = [
        '236' => '237',
        '236L' => '237L',
        '236R' => '237R',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::STANDS as $old => $new) {
            $this->updateStand($old, $new);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::STANDS as $old => $new) {
            $this->updateStand($new, $old);
        }
    }

    private function updateStand(string $oldIdentifier, string $newIdentifier): void
    {
        Stand::where('identifier', $oldIdentifier)
            ->airfield('EGLL')
            ->update(['identifier' => $newIdentifier]);
    }
}
