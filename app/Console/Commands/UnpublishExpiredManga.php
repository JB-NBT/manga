<?php

namespace App\Console\Commands;

use App\Models\Manga;
use Illuminate\Console\Command;

class UnpublishExpiredManga extends Command
{
    /**
     * Nom et signature de la commande
     *
     * @var string
     */
    protected $signature = 'manga:unpublish-expired';

    /**
     * Description de la commande
     *
     * @var string
     */
    protected $description = 'Retire automatiquement les mangas publics de plus d\'un an (protection copyright)';

    /**
     * Exécute la commande
     */
    public function handle()
    {
        $this->info('🔍 Recherche des mangas expirés...');

        $expiredMangas = Manga::expired()->get();

        if ($expiredMangas->isEmpty()) {
            $this->info('✅ Aucun manga expiré trouvé.');
            return 0;
        }

        $count = $expiredMangas->count();
        $this->warn("⚠️  {$count} manga(s) expiré(s) trouvé(s).");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($expiredMangas as $manga) {
            $manga->unpublish();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ {$count} manga(s) retiré(s) de la publication.");

        return 0;
    }
}
