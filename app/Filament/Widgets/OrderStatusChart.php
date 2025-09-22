<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Orders by Status';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statuses = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $colors = [
            'pending' => 'rgb(245, 158, 11)', // yellow
            'processing' => 'rgb(59, 130, 246)', // blue
            'shipped' => 'rgb(16, 185, 129)', // green
            'delivered' => 'rgb(34, 197, 94)', // green
            'cancelled' => 'rgb(239, 68, 68)', // red
            'refunded' => 'rgb(168, 85, 247)', // purple
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $statuses->pluck('count')->toArray(),
                    'backgroundColor' => $statuses->pluck('status')->map(function ($status) use ($colors) {
                        return $colors[$status] ?? 'rgb(156, 163, 175)';
                    })->toArray(),
                ],
            ],
            'labels' => $statuses->pluck('status')->map(function ($status) {
                return ucfirst(str_replace('_', ' ', $status));
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
