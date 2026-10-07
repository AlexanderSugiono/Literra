<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MigrateCovers extends Command
{
    protected $signature = 'covers:migrate';

    protected $description = 'Migrate book cover files from storage/app/public/covers to public/covers';

    public function handle()
    {
        $oldDisk = Storage::disk('public');
        $newDisk = Storage::disk('covers');

        $oldFiles = $oldDisk->files('covers');

        if (empty($oldFiles)) {
            $this->info('No old cover files found in storage/app/public/covers. Nothing to migrate.');
            return Command::SUCCESS;
        }

        $moved = 0;
        $updated = 0;

        foreach ($oldFiles as $oldFile) {
            $filename = basename($oldFile);

            if (!File::exists($oldDisk->path($oldFile))) {
                continue;
            }

            File::copy($oldDisk->path($oldFile), $newDisk->path($filename));
            $moved++;

            $updated += Book::withTrashed()
                ->where('cover_image', $oldFile)
                ->update(['cover_image' => $filename]);
        }

        $this->info("{$moved} file(s) moved to public/covers.");
        $this->info("{$updated} book record(s) updated to use the new covers disk path.");
        return Command::SUCCESS;
    }
}