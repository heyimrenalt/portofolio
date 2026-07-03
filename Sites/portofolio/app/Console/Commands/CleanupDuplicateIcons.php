<?php

namespace App\Console\Commands;

use App\Models\Skill;
use Illuminate\Console\Command;

class CleanupDuplicateIcons extends Command
{
    protected $signature   = 'skills:cleanup-duplicate-icons';
    protected $description = 'Null out icon_url on skills that have both icon_upload and icon_url set (prioritise upload).';

    public function handle(): int
    {
        $rows = Skill::whereNotNull('icon_upload')->whereNotNull('icon_url')->get();

        if ($rows->isEmpty()) {
            $this->info('No duplicate icon rows found. Nothing to do.');
            return 0;
        }

        $this->warn("Found {$rows->count()} skill(s) with both icon_upload and icon_url set:");

        foreach ($rows as $skill) {
            $this->line("  [ID {$skill->id}] {$skill->name} — clearing icon_url=\"{$skill->icon_url}\", keeping icon_upload=\"{$skill->icon_upload}\"");
            $skill->update(['icon_url' => null]);
        }

        $this->info('Done. All affected rows now have only icon_upload set.');
        return 0;
    }
}
