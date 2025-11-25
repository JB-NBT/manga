protected function schedule(Schedule $schedule)
{
    // Vérifie et retire les mangas expirés tous les jours à minuit
    $schedule->command('manga:unpublish-expired')->daily();
}
