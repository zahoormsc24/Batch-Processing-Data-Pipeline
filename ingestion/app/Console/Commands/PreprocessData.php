<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PreprocessData extends Command
{
    protected $signature = 'preprocess:data';
    protected $description = 'Clean transactions and store in clean_transactions table';

    public function handle()
    {
        $this->info('Cleaning data...');

        $batchSize = 1000;
        $offset = 0;

        do {
            $rows = DB::table('transactions')
                ->where('Quantity', '>', 0)
                ->whereNotNull('CustomerID')
                ->offset($offset)
                ->limit($batchSize)
                ->get();

            if ($rows->isEmpty()) break;

            $batch = [];
            foreach ($rows as $row) {
                $batch[] = [
                    'Invoice'     => $row->Invoice,
                    'StockCode'   => $row->StockCode,
                    'Description' => $row->Description,
                    'Quantity'    => $row->Quantity,
                    'InvoiceDate' => $row->InvoiceDate,
                    'Price'       => $row->Price,
                    'CustomerID'  => $row->CustomerID,
                    'Country'     => $row->Country,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            DB::table('clean_transactions')->insert($batch);

            $offset += $batchSize;
            $this->info("Processed batch ending at offset {$offset}");

        } while (true);

        $this->info('Data cleaning complete!');
    }
}
