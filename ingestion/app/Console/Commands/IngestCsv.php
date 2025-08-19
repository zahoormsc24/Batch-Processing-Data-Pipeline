<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IngestCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage: php artisan ingest:csv
     */
    protected $signature = 'ingest:csv';

    /**
     * The console command description.
     */
    protected $description = 'Ingest CSV file into MySQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path('app/online_retail_II.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $this->info("Ingesting data from $filePath ...");

        $rows = array_map('str_getcsv', file($filePath));
        $header = array_map('trim', array_shift($rows));

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            // Skip empty rows
            if (!$data || empty($data['Invoice'])) {
                continue;
            }

            DB::table('transactions')->insert([
                'Invoice'      => $data['Invoice'],
                'StockCode'    => $data['StockCode'],
                'Description'  => $data['Description'],
                'Quantity'     => (int) $data['Quantity'],
                'InvoiceDate'  => $data['InvoiceDate'],
                'Price'        => (float) $data['Price'],
                'CustomerID'   => $data['Customer ID'],
                'Country'      => $data['Country'],
            ]);
        }

        $this->info('CSV ingestion complete!');
        return 0;
    }
}
