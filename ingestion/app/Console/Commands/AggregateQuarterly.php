<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AggregateQuarterly extends Command
{
    protected $signature = 'aggregate:quarterly';
    protected $description = 'Aggregate quarterly sales per product and customer';

    public function handle()
    {
        $this->info('Aggregating quarterly sales...');

        // Delete old aggregates
        DB::table('quarterly_sales')->truncate();

        // Perform aggregation
        $results = DB::table('clean_transactions')
    ->select(
        DB::raw("CONCAT(YEAR(STR_TO_DATE(InvoiceDate, '%m/%d/%Y %H:%i')), '-Q', QUARTER(STR_TO_DATE(InvoiceDate, '%m/%d/%Y %H:%i'))) as year_quarter"),
        'StockCode',
        'CustomerID',
        DB::raw('SUM(Quantity) as total_quantity'),
        DB::raw('SUM(Quantity * Price) as total_sales')
    )
    ->whereNotNull('CustomerID')       // <-- filter out NULL
    ->where('CustomerID', '!=', '')    // <-- filter out empty strings
    ->groupBy('year_quarter', 'StockCode', 'CustomerID')
    ->get();


        // Insert results in chunks to avoid "too many placeholders" error
        $chunkSize = 1000; // adjust if needed
        $results->chunk($chunkSize)->each(function (Collection $chunk) {
            $batch = [];

            foreach ($chunk as $row) {
                $batch[] = [
		    'customer_id'   => $row->CustomerID,      // map CustomerID -> customer_id
		    'stock_code'    => $row->StockCode,       // map StockCode -> stock_code
		    'invoice_date'  => now(),                 // pick a date (or leave NULL if allowed)
		    'total_quantity'=> $row->total_quantity,
		    'total_sales'   => $row->total_sales,
		    'quarter'       => $row->year_quarter,   // map year_quarter -> quarter
		    'created_at'    => now(),
		];
            }

            if (!empty($batch)) {
                DB::table('quarterly_sales')->insert($batch);
            }
        });

        $this->info('Quarterly aggregation complete!');
    }
}

