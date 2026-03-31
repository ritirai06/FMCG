<?php

namespace App\Helpers;

use App\Models\OrderSequence;
use Carbon\Carbon;

class OrderHelper
{
    /**
     * Generate unique order number in format: ORD-YYYYMMDD-XXXX
     * Example: ORD-20260222-0001
     *
     * @return string
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = OrderSequence::getNextSequence();
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return "ORD-{$date}-{$sequenceFormatted}";
    }

    /**
     * Get order status badge color
     *
     * @param string $status
     * @return string
     */
    public static function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'Pending' => 'badge-warning',
            'Approved' => 'badge-info',
            'Packed' => 'badge-primary',
            'Out for Delivery' => 'badge-secondary',
            'Delivered' => 'badge-success',
            default => 'badge-dark'
        };
    }

    /**
     * Get order status icon
     *
     * @param string $status
     * @return string
     */
    public static function getStatusIcon(string $status): string
    {
        return match($status) {
            'Pending' => 'fas fa-clock',
            'Approved' => 'fas fa-check-circle',
            'Packed' => 'fas fa-box',
            'Out for Delivery' => 'fas fa-truck',
            'Delivered' => 'fas fa-check-double',
            default => 'fas fa-question-circle'
        };
    }

    /**
     * Get all order statuses
     *
     * @return array
     */
    public static function getOrderStatuses(): array
    {
        return [
            'Pending' => 'Pending',
            'Approved' => 'Approved',
            'Packed' => 'Packed',
            'Out for Delivery' => 'Out for Delivery',
            'Delivered' => 'Delivered'
        ];
    }

    /**
     * Can user change order status
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @param string $userRole
     * @return bool
     */
    public static function canChangeStatus(string $currentStatus, string $newStatus, string $userRole): bool
    {
        $transitions = [
            'admin' => ['Pending', 'Approved', 'Packed', 'Out for Delivery', 'Delivered'],
            'sales' => ['Pending', 'Approved'],
            'delivery' => ['Out for Delivery', 'Delivered']
        ];

        return in_array($newStatus, $transitions[$userRole] ?? []);
    }
}
