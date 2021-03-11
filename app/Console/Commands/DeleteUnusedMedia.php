<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteUnusedMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sweep';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sweeping the database for unused media files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Media::whereNull('model_id')
            ->where('created_at', '<=', now()->subMinute())
            ->chunk(100, function($media){
                $media->each( function ($item){
                    Storage::disk('public')->delete('media/'. $item->user_id .'/' .$item->created_at->format('Y'). '/' . $item->created_at->format('m'). '/' . $item->filename);
                    $item->delete();
                });
            });
    }
}
