<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::latest()->paginate(20);

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'table_number' => ['required', 'string', 'max:10', 'unique:meja,table_number'],
        ], [
            'table_number.required' => 'Nomor meja wajib diisi.',
            'table_number.unique' => 'Nomor meja sudah terdaftar.',
            'table_number.max' => 'Nomor meja maksimal 10 karakter.',
        ]);

        $table = Table::create([
            'table_number' => $request->table_number,
            'status' => 'available',
        ]);

        $this->generateQr($table);

        return redirect()->route('admin.tables.index')
            ->with('success', "Meja {$table->table_number} berhasil ditambahkan.");
    }

    public function destroy(Table $table): RedirectResponse
    {
        if ($table->qr_code_path) {
            Storage::disk('public')->delete($table->qr_code_path);
        }
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Bikin ulang file QR Code buat meja tertentu kalau rusak atau ganti.
     */
    public function regenerateQr(Table $table): RedirectResponse
    {
        $this->generateQr($table);

        return back()->with('success', "QR Code meja {$table->table_number} berhasil dibuat ulang.");
    }

    /**
     * Halaman print QR Code branded per meja — stream sebagai PDF.
     */
    public function printQr(Table $table)
    {
        abort_if(! $table->qr_code_path, 404, 'QR Code belum di-generate.');

        // Baca SVG dan embed langsung sebagai base64 agar DomPDF bisa render
        $svgContent = Storage::disk('public')->get($table->qr_code_path);
        $qrBase64 = 'data:image/svg+xml;base64,'.base64_encode($svgContent);
        $orderUrl = route('order.menu', $table);

        $pdf = Pdf::loadView('admin.tables.pdf-qr', compact('table', 'qrBase64', 'orderUrl'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
            ]);

        return $pdf->download("qr_meja_{$table->table_number}.pdf");
    }

    /**
     * Generate & simpan QR Code dinamis (token) ke storage.
     */
    private function generateQr(Table $table): void
    {
        $table->generateQr();
    }
}
