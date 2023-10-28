<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    const DATA = [
        ["code" => "CRJ1", "wingspan" => "21.23", "length" => "26.77"],
        ["code" => "CRJ2", "wingspan" => "21.23", "length" => "26.77"],
        ["code" => "CRJ7", "wingspan" => "23.25", "length" => "32.48"],
        ["code" => "CRJ9", "wingspan" => "23.25", "length" => "36.34"],
        ["code" => "AT43", "wingspan" => "24.57", "length" => "22.67"],
        ["code" => "AT44", "wingspan" => "24.57", "length" => "22.67"],
        ["code" => "AT45", "wingspan" => "24.57", "length" => "22.67"],
        ["code" => "AT46", "wingspan" => "24.57", "length" => "22.67"],
        ["code" => "DH8A", "wingspan" => "25.89", "length" => "22.25"],
        ["code" => "DH8B", "wingspan" => "25.89", "length" => "22.25"],
        ["code" => "E170", "wingspan" => "26.00", "length" => "29.90"],
        ["code" => "E75S", "wingspan" => "26.00", "length" => "31.68"],
        ["code" => "CRJX", "wingspan" => "26.18", "length" => "39.13"],
        ["code" => "AT72", "wingspan" => "27.05", "length" => "27.17"],
        ["code" => "AT73", "wingspan" => "27.05", "length" => "27.17"],
        ["code" => "AT75", "wingspan" => "27.05", "length" => "27.17"],
        ["code" => "AT76", "wingspan" => "27.05", "length" => "27.17"],
        ["code" => "DH8C", "wingspan" => "27.40", "length" => "25.70"],
        ["code" => "DH8D", "wingspan" => "28.42", "length" => "32.83"],
        ["code" => "E75L", "wingspan" => "28.65", "length" => "31.68"],
        ["code" => "E190", "wingspan" => "28.72", "length" => "36.24"],
        ["code" => "E195", "wingspan" => "28.72", "length" => "38.67"],
        ["code" => "ATP", "wingspan" => "30.63", "length" => "26.00"],
        ["code" => "E275", "wingspan" => "31.39", "length" => "32.37"],
        ["code" => "MD81", "wingspan" => "32.85", "length" => "45.02"],
        ["code" => "MD82", "wingspan" => "32.85", "length" => "45.02"],
        ["code" => "MD83", "wingspan" => "32.85", "length" => "45.02"],
        ["code" => "MD87", "wingspan" => "32.85", "length" => "39.75"],
        ["code" => "MD88", "wingspan" => "32.85", "length" => "45.02"],
        ["code" => "MD90", "wingspan" => "32.87", "length" => "46.50"],
        ["code" => "E290", "wingspan" => "33.72", "length" => "36.20"],
        ["code" => "A318", "wingspan" => "34.10", "length" => "31.45"],
        ["code" => "BCS1", "wingspan" => "35.10", "length" => "35.00"],
        ["code" => "BCS3", "wingspan" => "35.10", "length" => "38.71"],
        ["code" => "E295", "wingspan" => "35.12", "length" => "41.60"],
        ["code" => "B736", "wingspan" => "35.79", "length" => "31.24"],
        ["code" => "B737", "wingspan" => "35.79", "length" => "33.63"],
        ["code" => "B738", "wingspan" => "35.79", "length" => "39.47"],
        ["code" => "B739", "wingspan" => "35.79", "length" => "42.11"],
        ["code" => "A319", "wingspan" => "35.80", "length" => "33.84"],
        ["code" => "A320", "wingspan" => "35.80", "length" => "37.57"],
        ["code" => "A321", "wingspan" => "35.80", "length" => "44.51"],
        ["code" => "A19N", "wingspan" => "35.80", "length" => "33.84"],
        ["code" => "A20N", "wingspan" => "35.80", "length" => "37.57"],
        ["code" => "A21N", "wingspan" => "35.80", "length" => "44.51"],
        ["code" => "B37M", "wingspan" => "35.92", "length" => "35.56"],
        ["code" => "B38M", "wingspan" => "35.92", "length" => "39.52"],
        ["code" => "B39M", "wingspan" => "35.92", "length" => "42.11"],
        ["code" => "B3XM", "wingspan" => "35.92", "length" => "43.79"],
        ["code" => "B752", "wingspan" => "38.05", "length" => "47.32"],
        ["code" => "B753", "wingspan" => "38.06", "length" => "54.43"],
        ["code" => "A310", "wingspan" => "43.90", "length" => "46.66"],
        ["code" => "A30B", "wingspan" => "44.84", "length" => "54.08"],
        ["code" => "A306", "wingspan" => "44.84", "length" => "53.61"],
        ["code" => "A3ST", "wingspan" => "44.84", "length" => "56.15"],
        ["code" => "B762", "wingspan" => "47.57", "length" => "48.51"],
        ["code" => "B763", "wingspan" => "47.57", "length" => "54.94"],
        ["code" => "DC10", "wingspan" => "50.39", "length" => "55.55"],
        ["code" => "B764", "wingspan" => "51.92", "length" => "61.37"],
        ["code" => "MD11", "wingspan" => "51.97", "length" => "61.60"],
        ["code" => "B788", "wingspan" => "60.12", "length" => "56.72"],
        ["code" => "B789", "wingspan" => "60.12", "length" => "62.81"],
        ["code" => "B78X", "wingspan" => "60.12", "length" => "68.30"],
        ["code" => "A332", "wingspan" => "60.30", "length" => "58.82"],
        ["code" => "A333", "wingspan" => "60.30", "length" => "63.69"],
        ["code" => "A337", "wingspan" => "60.30", "length" => "63.10"],
        ["code" => "A342", "wingspan" => "60.30", "length" => "59.42"],
        ["code" => "A343", "wingspan" => "60.30", "length" => "63.69"],
        ["code" => "B772", "wingspan" => "60.93", "length" => "63.73"],
        ["code" => "B773", "wingspan" => "60.93", "length" => "73.86"],
        ["code" => "A345", "wingspan" => "63.45", "length" => "67.93"],
        ["code" => "A346", "wingspan" => "63.45", "length" => "75.36"],
        ["code" => "A338", "wingspan" => "64.00", "length" => "58.82"],
        ["code" => "A339", "wingspan" => "64.00", "length" => "63.69"],
        ["code" => "A359", "wingspan" => "64.75", "length" => "66.80"],
        ["code" => "A35K", "wingspan" => "64.75", "length" => "73.79"],
        ["code" => "B77L", "wingspan" => "64.80", "length" => "63.73"],
        ["code" => "B77W", "wingspan" => "64.80", "length" => "73.86"],
        ["code" => "B778", "wingspan" => "64.84", "length" => "70.86"],
        ["code" => "B779", "wingspan" => "64.84", "length" => "76.73"],
        ["code" => "B744", "wingspan" => "64.92", "length" => "70.67"],
        ["code" => "B748", "wingspan" => "68.40", "length" => "76.25"],
        ["code" => "A388", "wingspan" => "79.75", "length" => "72.73"],
    ];
    

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('aircraft')->upsert(self::DATA, ['code'], ['wingspan', 'length']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
