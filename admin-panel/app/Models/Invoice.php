<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'order_id', 'party_id', 'created_by', 'assigned_delivery',
        'date', 'due_date', 'amount', 'tax', 'discount', 'status', 'notes',
    ];

    protected $casts = [
        'date'     => 'date',
        'due_date' => 'date',
        'amount'   => 'decimal:2',
        'tax'      => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function party()
    {
        return $this->belongsTo(Store::class, 'party_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveryUser()
    {
        return $this->belongsTo(User::class, 'assigned_delivery');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->amount + (float) $this->tax - (float) $this->discount;
    }

    public static function generateInvoiceNumber(): string
    {
        $last = static::orderByDesc('id')->value('invoice_number');
        $num  = 1001;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $num = (int) $m[1] + 1;
        }
        return 'INV-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeForRole($query, $user)
    {
        if (!$user) return $query;
        return match ($user->role) {
            'sales'    => $query->where('created_by', $user->id),
            'delivery' => $query->where('assigned_delivery', $user->id),
            default    => $query,
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
