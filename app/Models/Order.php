<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUSES = ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];

    public const PAYMENT_STATUSES = ['unpaid', 'waiting_verification', 'paid', 'failed'];

    /** Metode pembayaran yang butuh upload bukti */
    public const PAYMENT_NEEDS_PROOF = ['manual', 'qris'];

    protected $fillable = [
        'user_id', 'invoice_number', 'name', 'phone', 'email',
        'address', 'city', 'delivery_date', 'delivery_time',
        'payment_method', 'payment_proof', 'payment_status', 'payment_note',
        'notes', 'is_gift', 'total', 'status',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'is_gift'       => 'boolean',
        'total'         => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalLabelAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    /** Apakah metode pembayaran ini butuh upload bukti */
    public function needsPaymentProof(): bool
    {
        return in_array($this->payment_method, self::PAYMENT_NEEDS_PROOF);
    }

    /** Kelas warna badge status pesanan */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'bg-amber-100 text-amber-800',
            'diproses'   => 'bg-sky-100 text-sky-800',
            'dikirim'    => 'bg-indigo-100 text-indigo-800',
            'selesai'    => 'bg-lime-100 text-lime-800',
            'dibatalkan' => 'bg-rose-100 text-rose-700',
            default      => 'bg-stone-100 text-stone-700',
        };
    }

    /** Kelas warna badge status pembayaran */
    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid'                => 'bg-rose-100 text-rose-700',
            'waiting_verification'  => 'bg-amber-100 text-amber-800',
            'paid'                  => 'bg-lime-100 text-lime-800',
            'failed'                => 'bg-red-100 text-red-800',
            default                 => 'bg-stone-100 text-stone-700',
        };
    }

    /** Label status pembayaran dalam Bahasa Indonesia */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid'                => '⏳ Belum Bayar',
            'waiting_verification'  => '🔍 Menunggu Verifikasi',
            'paid'                  => '✅ Lunas',
            'failed'                => '❌ Gagal',
            default                 => ucfirst($this->payment_status),
        };
    }

    /** Label nama metode pembayaran */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cod'      => '💵 COD (Bayar di Tempat)',
            'transfer' => '🏦 Transfer Bank',
            'ewallet'  => '📱 E-Wallet',
            'manual'   => '🏦 Transfer Manual',
            'qris'     => '⚡ QRIS',
            default    => strtoupper($this->payment_method),
        };
    }
}
