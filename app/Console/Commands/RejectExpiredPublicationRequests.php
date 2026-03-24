<?php

namespace App\Console\Commands;

use App\Models\PublicationRequest;
use Illuminate\Console\Command;

class RejectExpiredPublicationRequests extends Command
{
    protected $signature = 'publication:reject-expired';

    protected $description = 'Refuse automatiquement les demandes de publication en attente depuis plus de 30 jours';

    public function handle()
    {
        $this->info('Recherche des demandes de publication expirées...');

        $expired = PublicationRequest::where('statut', 'en_attente')
            ->where('created_at', '<', now()->subDays(30))
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Aucune demande expirée trouvée.');
            return 0;
        }

        $count = $expired->count();
        $this->warn("{$count} demande(s) expirée(s) trouvée(s).");

        foreach ($expired as $request) {
            $request->update([
                'statut' => 'refuse',
                'message_admin' => 'Demande automatiquement refusée : délai de 30 jours dépassé sans réponse du modérateur.',
                'date_traitement' => now(),
            ]);
        }

        $this->info("{$count} demande(s) automatiquement refusée(s).");

        return 0;
    }
}
