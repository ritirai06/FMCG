<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Helpers\OrderHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Collection;

class OrderListService
{
    /**
     * Get role-filtered orders with pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getFilteredOrders(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $query = Order::with(['store', 'createdBy', 'assignedDelivery', 'items.product']);

        // Role-based filtering
        $query = $this->applyRoleFilter($query, $user);

        // Apply additional filters
        $query = $this->applyStatusFilter($query, $filters);
        $query = $this->applyStoreFilter($query, $filters);
        $query = $this->applyDateRangeFilter($query, $filters);

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Apply role-based filtering to order query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyRoleFilter($query, User $user)
    {
        if ($user->isAdmin()) {
            // Admin sees all orders
            return $query;
        } elseif ($user->isSales()) {
            // Sales sees only orders they created
            return $query->where('created_by', $user->id);
        } elseif ($user->isDelivery()) {
            // Delivery sees only orders assigned to them
            return $query->where('assigned_delivery', $user->id);
        }

        // Fallback: if no role, deny access (handled by middleware)
        return $query->whereNull('id'); // Returns empty result
    }

    /**
     * Apply status filter to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyStatusFilter($query, array $filters)
    {
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Apply store filter to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyStoreFilter($query, array $filters)
    {
        if (!empty($filters['store']) && $filters['store'] !== 'all') {
            $query->where('store_id', $filters['store']);
        }

        return $query;
    }

    /**
     * Apply date range filter to query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDateRangeFilter($query, array $filters)
    {
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Get available statuses for current user
     *
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        return OrderHelper::getOrderStatuses();
    }

    /**
     * Get status badge information (class and icon)
     *
     * @param string $status
     * @return array
     */
    public function getStatusBadgeInfo(string $status): array
    {
        return [
            'class' => OrderHelper::getStatusBadgeClass($status),
            'icon' => OrderHelper::getStatusIcon($status),
        ];
    }

    /**
     * Check if current user can transition to new status
     *
     * @param Order $order
     * @param string $newStatus
     * @return bool
     */
    public function canTransitionStatus(Order $order, string $newStatus): bool
    {
        $user = Auth::user();
        return OrderHelper::canChangeStatus($order->status, $newStatus, $user->role);
    }

    /**
     * Get orders summary for dashboard
     *
     * @return array
     */
    public function getOrdersSummary(): array
    {
        $user = Auth::user();
        $query = Order::query();
        $amountColumn = $this->getOrderAmountColumn();

        // Apply role filter
        if ($user->isSales()) {
            $query->where('created_by', $user->id);
        } elseif ($user->isDelivery()) {
            $query->where('assigned_delivery', $user->id);
        }

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'Pending')->count(),
            'approved' => (clone $query)->where('status', 'Approved')->count(),
            'packed' => (clone $query)->where('status', 'Packed')->count(),
            'out_for_delivery' => (clone $query)->where('status', 'Out for Delivery')->count(),
            'delivered' => (clone $query)->where('status', 'Delivered')->count(),
            'cancelled' => (clone $query)->where('status', 'Cancelled')->count(),
            'total_amount' => (clone $query)->sum($amountColumn),
        ];
    }

    /**
     * Get delivery persons for assignment
     *
     * @return Collection
     */
    public function getDeliveryPersons(): Collection
    {
        return User::byRole('delivery')->active()->orderBy('name')->get();
    }

    /**
     * Get stores for filtering (role-based)
     *
     * @return Collection
     */
    public function getStoresForFilter(): Collection
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return \App\Models\Store::orderBy('store_name')->get();
        } elseif ($user->isSales()) {
            // Sales can see stores they have orders for
            return \App\Models\Store::whereHas('orders', function ($q) {
                $q->where('created_by', Auth::id());
            })->orderBy('store_name')->get();
        }

        return collect(); // Empty for delivery
    }

    /**
     * Validate order access for current user
     *
     * @param Order $order
     * @return bool
     */
    public function canAccessOrder(Order $order): bool
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        } elseif ($user->isSales()) {
            return $order->created_by === $user->id;
        } elseif ($user->isDelivery()) {
            return $order->assigned_delivery === $user->id;
        }

        return false;
    }

    /**
     * Validate order edit permission for current user
     *
     * @param Order $order
     * @return bool
     */
    public function canEditOrder(Order $order): bool
    {
        $user = Auth::user();
        return $user->isAdmin();
    }

    /**
     * Validate order cancellation permission
     *
     * @param Order $order
     * @return bool
     */
    public function canCancelOrder(Order $order): bool
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isSales() && $order->created_by === $user->id) {
            return !in_array($order->status, ['Out for Delivery', 'Delivered', 'Cancelled']);
        }

        return false;
    }

    /**
     * Get order statistics for a date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getOrderStatistics(string $startDate, string $endDate): array
    {
        $user = Auth::user();
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        $amountColumn = $this->getOrderAmountColumn();

        if ($user->isSales()) {
            $query->where('created_by', $user->id);
        } elseif ($user->isDelivery()) {
            $query->where('assigned_delivery', $user->id);
        }

        $orders = $query->get();

        return [
            'total_orders' => $orders->count(),
            'pending_count' => $orders->where('status', 'Pending')->count(),
            'delivered_count' => $orders->where('status', 'Delivered')->count(),
            'total_revenue' => $orders->sum($amountColumn),
            'avg_order_value' => $orders->count() > 0 ? $orders->sum($amountColumn) / $orders->count() : 0,
        ];
    }

    private function getOrderAmountColumn(): string
    {
        return Schema::hasColumn('orders', 'total_amount') ? 'total_amount' : 'amount';
    }
}
