<?php

namespace App\Imports;

use App\BaseFunctionalTestCase;
use App\Models\Srd\SrdNote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\BeforeSheet;

class SrdNotesImportTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Empty the table first
        SrdNote::all()->each(function (SrdNote $note) {
            $note->delete();
        });
    }

    public function testItImportsASingleNote()
    {
        $rows = (new Collection())->push(['Note 123'])
            ->push(['This is the first line of the note'])
            ->push(['This is the second line of the note']);

        (new SrdNotesImport())->collection($rows);
        $notes = SrdNote::all();

        $this->assertCount(1, $notes);
        $this->assertEquals(123, $notes->first()->id);

        $expectedNoteText = 'This is the first line of the note' .
            PHP_EOL .
            'This is the second line of the note';

        $this->assertEquals(
            $expectedNoteText,
            $notes->first()->note_text
        );
    }

    public function testItStripsNewlinesFromEndOfNote()
    {
        $rows = (new Collection())->push(['Note 123'])
            ->push(['This is the first line of the note'])
            ->push(['This is the second line of the note'])
            ->push(['']);

        (new SrdNotesImport())->collection($rows);
        $notes = SrdNote::all();

        $this->assertCount(1, $notes);
        $this->assertEquals(123, $notes->first()->id);

        $expectedNoteText = 'This is the first line of the note' .
            PHP_EOL .
            'This is the second line of the note';

        $this->assertEquals(
            $expectedNoteText,
            $notes->first()->note_text
        );
    }

    public function testItImportsMultipleNotes()
    {
        $rows = (new Collection())->push(['Note 123'])
            ->push(['This is the first line of the first note'])
            ->push(['This is the second line of the first note'])
            ->push(['Note 456'])
            ->push(['This is the first line of the second note'])
            ->push(['This is the second line of the second note']);

        (new SrdNotesImport())->collection($rows);
        $notes = SrdNote::all();

        $this->assertCount(2, $notes);
        $this->assertEquals(123, $notes[0]->id);

        $expectedNoteTextFirst = 'This is the first line of the first note' .
            PHP_EOL .
            'This is the second line of the first note';

        $this->assertEquals(
            $expectedNoteTextFirst,
            $notes[0]->note_text
        );

        $this->assertEquals(456, $notes[1]->id);

        $expectedNoteTextSecond = 'This is the first line of the second note' .
            PHP_EOL .
            'This is the second line of the second note';

        $this->assertEquals(
            $expectedNoteTextSecond,
            $notes[1]->note_text
        );
    }

    public function testItSubscribesToBeforeSheetEvents()
    {
        $this->assertArrayHasKey(BeforeSheet::class, (new SrdNotesImport())->registerEvents());
    }
}
