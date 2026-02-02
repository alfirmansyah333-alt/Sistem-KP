<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PenerimaanKP;
use App\Models\Perusahaan;

class MigratePerusahaanData extends Command
{
    protected $signature = 'migrate:perusahaan-data';
    protected $description = 'Migrate existing penerimaan data to create perusahaan records';

    public function handle()
    {
        $this->info('Starting migration of perusahaan data...');

        $penerimaanWithoutPerusahaan = PenerimaanKP::whereNull('perusahaan_id')
            ->whereNotNull('nama_perusahaan')
            ->get();

        if ($penerimaanWithoutPerusahaan->isEmpty()) {
            $this->info('No data to migrate.');
            return Command::SUCCESS;
        }

        $this->info("Found {$penerimaanWithoutPerusahaan->count()} records to migrate.");

        $bar = $this->output->createProgressBar($penerimaanWithoutPerusahaan->count());
        $bar->start();

        foreach ($penerimaanWithoutPerusahaan as $penerimaan) {
            // Create or get perusahaan
            $perusahaan = Perusahaan::firstOrCreate(
                ['nama_perusahaan' => $penerimaan->nama_perusahaan],
                ['is_mitra' => false]
            );

            // Update penerimaan with perusahaan_id
            $penerimaan->update(['perusahaan_id' => $perusahaan->id]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migration completed successfully!');
        $this->info('Total perusahaan created: ' . Perusahaan::count());

        return Command::SUCCESS;
    }
}
