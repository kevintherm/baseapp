<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

abstract class BaseModelChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $period = '30 days';
    protected static ?string $description = '';

    abstract protected function getQueries(): array;
    abstract protected function getColors(): array;

    protected static function setPeriod(string $period): void
    {
        static::$period = $period;
        static::$description = "Last {$period}";
    }

    protected function getData(): array
    {
        $this->setPeriod(empty($this->filters['interval']) ? '30 days' : $this->filters['interval']);

        preg_match('/(\d+)\s*(day|week|month|year)s?/', static::$period, $matches);
        $amount = (int)$matches[1];
        $unit = $matches[2];

        $endDate = now();
        $startDate = now()->sub($amount, "{$unit}s");

        $datasets = [];
        $firstTrend = null;
        foreach ($this->getQueries() as $index => $queryData) {
            $query = $queryData['query'];
            $label = $queryData['label'];

            $trend = match ($unit) {
                'day' => Trend::query($query)->between($startDate, $endDate)->perDay()->count(),
                'week' => Trend::query($query)->between($startDate, $endDate)->perWeek()->count(),
                'month' => Trend::query($query)->between($startDate, $endDate)->perMonth()->count(),
                'year' => Trend::query($query)->between($startDate, $endDate)->perYear()->count(),
            };

            if ($index === 0) {
                $firstTrend = $trend;
            }

            $colors = $this->getColors()[$index] ?? null;
            $dataset = [
                'label' => $label,
                'data' => $trend->map(fn ($value) => $value->aggregate),
            ];

            if ($colors) {
                $dataset = array_merge($dataset, [
                    'borderColor' => "rgb({$colors['border']})",
                    'backgroundColor' => "rgba({$colors['background']}, 0.3)",
                    'pointBackgroundColor' => "rgba({$colors['point']})",
                    'fillColor' => "rgba({$colors['fill']}, 0.3)"
                ]);
            }

            $dataset['tension'] = 0.4;
            $dataset['fill'] = isset($colors['fill']);

            $datasets[] = $dataset;
        }

        return [
            'datasets' => $datasets,
            'labels' => $firstTrend->map(function ($value) use ($unit) {
                $date = Carbon::parse($value->date);
                switch ($unit) {
                    case 'day':
                        return $date->format('M d');
                    case 'week':
                        $tokenDate = explode('-', $value->date);
                        $date = Carbon::create()->year((int)$tokenDate[0])->week((int)$tokenDate[1]);
                        return "Week {$date->week()} of {$date->year}";
                    case 'month':
                        return $date->format('M') . ' '.  $date->year;
                    case 'year':
                        return $value->date;
                }
            })
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
