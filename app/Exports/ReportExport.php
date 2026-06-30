<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private string $date) {}

    public function collection()
    {
        return Order::with(['table', 'orderItems.product', 'payment'])
            ->whereBetween('created_at', ["{$this->date} 00:00:00", "{$this->date} 23:59:59"])
            ->where('status', 'completed')
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['No. Antrean', 'Meja', 'Item Pesanan', 'Total (Rp)', 'Metode Bayar', 'Status', 'Waktu'];
    }

    public function map($order): array
    {
        return [
            $order->queue_number,
            $order->table->table_number,
            $order->orderItems->map(fn ($i) => $i->quantity.'x '.$i->product->name)->join(', '),
            $order->total_amount,
            $order->payment?->method === 'tunai' ? 'Tunai' : 'Digital',
            ucfirst($order->status),
            $order->created_at->format('H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
