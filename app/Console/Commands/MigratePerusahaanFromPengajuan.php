<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanKP;
use App\Models\Perusahaan;

class MigratePerusahaanFromPengajuan extends Command
{
    protected $signature = 'migrate:perusahaan-from-pengajuan';
    protected $description = 'Migrate perusahaan data from pengajuan_kp to perusahaans table';

    public function handle()
    {
        $this->info('Starting migration of perusahaan from pengajuan KP...');

        $pengajuans = PengajuanKP::all();

        if ($pengajuans->isEmpty()) {
            $this->info('No pengajuan data found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pengajuans->count()} pengajuan records.");

        $bar = $this->output->createProgressBar($pengajuans->count());
        $bar->start();

        $created = 0;
        $updated = 0;

        foreach ($pengajuans as $pengajuan) {
            $isMitra = $pengajuan->mitra_dengan_perusahaan === 'iya';
            
            $perusahaan = Perusahaan::updateOrCreate(
                ['nama_perusahaan' => $pengajuan->perusahaan_tujuan],
                ['is_mitra' => $isMitra]
            );

            if ($perusahaan->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migration completed successfully!');
        $this->info("Perusahaan created: {$created}");
        $this->info("Perusahaan updated: {$updated}");
        $this->info('Total perusahaan in database: ' . Perusahaan::count());

        return Command::SUCCESS;
    }
}
