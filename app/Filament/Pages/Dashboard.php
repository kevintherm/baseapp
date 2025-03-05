<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    private function getIntervalOptions()
    {
        $defaultOptions = [
            '7 days' => '7 days',
            '30 days' => '30 days',
            '4 weeks' => '4 weeks',
            '2 months' => '2 months',
            '6 months' => '6 months',
            '12 months' => '12 months',
            '2 years' => '2 years',
        ];

        return Cache::remember('interval_options', '', function () use ($defaultOptions) {
            return $defaultOptions;
        });
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('interval')
                            ->options($this->getIntervalOptions())
                            ->default('12 months')
                            ->createOptionForm([
                                Section::make()
                                    ->schema([
                                        TextInput::make('customInterval')
                                            ->label('Custom Interval')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(99)
                                            ->placeholder('Enter a number')
                                            ->required(),
                                        Select::make('customIntervalUnit')
                                            ->label('Custom Interval Unit')
                                            ->options([
                                                'days' => 'day(s)',
                                                'weeks' => 'week(s)',
                                                'months' => 'month(s)',
                                                'years' => 'year(s)',
                                            ])
                                            ->default('days')
                                            ->native(false)
                                            ->required(),
                                        Toggle::make('rememberCustomInterval')
                                            ->label('Remember Custom Interval')
                                            ->default(false),
                                    ])
                                    ->columns(2)
                            ])
                            ->createOptionUsing(function (array $data, $set) {
                                $options = $this->getIntervalOptions();
                                $customInterval = $data['customInterval'] . ' ' . $data['customIntervalUnit'];
                                $options[$customInterval] = $customInterval;

                                if ($data['rememberCustomInterval']) Cache::forever('interval_options', $options);
                                else Cache::put('interval_options', $options, now()->addHours(6));

                                Notification::make()
                                    ->title('Created custom interval!')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->columns(3),
            ]);
    }
}
