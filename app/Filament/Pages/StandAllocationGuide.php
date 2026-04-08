<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\View\View;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class StandAllocationGuide extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-x-circle';

    protected string $view = 'filament.pages.markdown-page';

    protected static string | \UnitEnum | null $navigationGroup = 'Documentation';

    protected static ?string $slug = 'stand-allocation';

    public function getHeader(): View
    {
        return view('empty');
    }

    protected function getViewData(): array
    {
        return [
            'md' => app(MarkdownRenderer::class)->toHtml(file_get_contents(base_path('docs/guides/StandAllocation.md'))),
        ];
    }
}
