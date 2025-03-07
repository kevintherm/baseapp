<?php

namespace App\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

abstract class BaseModelStats extends BaseWidget
{
    use InteractsWithPageFilters;

    abstract protected function getStatsData(): array;

    protected function getModelStats(string $modelClass, array $excludeConditions = [], string $period = '7 days', string $icon = 'heroicon-o-users'): array
    {
        preg_match('/(\d+)\s*(day|week|month|year)s?/', $period, $matches);
        $amount = (int)$matches[1];
        $unit = $matches[2];

        $lastPeriod = now()->sub($amount, "{$unit}s");
        $query = $modelClass::query();

        foreach ($excludeConditions as $condition) {
            if (isset($condition['whereNotIn'])) {
                $query->whereNotIn($condition['whereNotIn']['column'], $condition['whereNotIn']['values']);
            }
        }

        $currentCount = $query->count();
        $lastPeriodCount = (clone $query)
            ->where('created_at', '<=', $lastPeriod)
            ->count();

        $increased = $currentCount > $lastPeriodCount;
        $percentageChange = $lastPeriodCount > 0 ? (($currentCount - $lastPeriodCount) / $lastPeriodCount) * 100 : 0;
        $dailyCounts = [];

        switch ($unit) {
            case 'day':
                for ($i = $amount; $i > 0; $i--) {
                    $date = now()->subDays($i);
                    $dailyCounts[] = (clone $query)->whereDate('created_at', $date)->count();
                }
                break;
            case 'week':
                for ($i = $amount; $i > 0; $i--) {
                    $date = now()->subWeeks($i);
                    $dailyCounts[] = (clone $query)->whereBetween('created_at', [
                        $date->startOfWeek(),
                        $date->endOfWeek(),
                    ])->count();
                }
                break;
            case 'month':
                for ($i = $amount; $i > 0; $i--) {
                    $date = now()->subMonths($i);
                    $dailyCounts[] = (clone $query)->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
                break;
            case 'year':
                for ($i = $amount; $i > 0; $i--) {
                    $date = now()->subYears($i);
                    $dailyCounts[] = (clone $query)->whereYear('created_at', $date->year)->count();
                }
                break;
        }

        $showChart = array_sum($dailyCounts) >= 5 || 1;

        return [
            'model' => $modelClass,
            'label' => str_replace('App\Models\\', '', $modelClass) . sprintf(" (Last %s)", $period),
            'value' => $currentCount,
            'icon' => $icon,
            'description' => sprintf("%.0f%% %s",  abs($percentageChange), $increased ? 'increase' : 'decrease'),
            'color' => ($showChart && $percentageChange != 0) ? Color::{$increased ? 'Green' : 'Red'} : Color::Gray,
            'descriptionIcon' => ($showChart && $percentageChange != 0) ? ($increased
                ? 'heroicon-o-arrow-trending-up'
                : 'heroicon-o-arrow-trending-down') : 'heroicon-o-arrows-up-down',
            'chart' => ($showChart && $percentageChange != 0) ? $dailyCounts : []
        ];
    }

    protected function getStats(): array
    {
        $rawWidgets = $this->getStatsData();
        $widgets = [];

        foreach($rawWidgets as $key => $widget) {
            $widgets[] = Stat::make(ucfirst($key), $widget['value'])
                ->label($widget['label'])
                ->color($widget['color'])
                ->icon($widget['icon'])
                ->descriptionIcon($widget['descriptionIcon'])
                ->description($widget['description'])
                ->chart($widget['chart']);
        }

        return $widgets;
    }
}
