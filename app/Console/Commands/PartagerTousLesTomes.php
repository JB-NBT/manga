<?php

namespace App\Console\Commands;

use App\Models\Tome;
use Illuminate\Console\Command;

class PartagerTousLesTomes extends Command
{
    protected $signature = 'tomes:partager-tous';
    protected $description = 'Marque tous les tomes possédés comme partagés et disponibles au prêt';

    public function handle(): void
    {
        $count = Tome::where('possede', true)
            ->where('partage', false)
            ->update([
                'partage' => true,
                'statut_pret' => 'disponible',
            ]);

        $this->info("{$count} tome(s) mis à jour.");
    }
}
