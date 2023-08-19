<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptCounter extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function generateReceiptNumber()
    {
        // Retrieve or create the ReceiptCounter record
        $counter = ReceiptCounter::firstOrCreate([], ['last_receipt_number' => 0]);

        // Increment the last_receipt_number by 1
        $counter->increment('last_receipt_number');

        // Generate the receipt number with the fixed prefix and the incremented part
        $receiptNumber = 'RECEIPT-' . str_pad($counter->last_receipt_number, 5, '0', STR_PAD_LEFT);

        return $receiptNumber;
    }
}
