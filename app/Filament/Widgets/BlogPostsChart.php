<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Blog Posts';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    // Add custom styles to ensure full-width rendering
    // protected function getStyles(): array
    // {
    //     return [
    //         'chart' => [
    //             'width' => '100%',  // Set the width to 100% of the parent container
    //             'max-width' => '100%',  // Ensure it does not exceed the full width
    //         ],
    //     ];
    // }

    protected function getData(): array
    {
        return [
            'datasets' => array_merge($this->bar()['datasets'], $this->bar2()['datasets']),
            'labels' => $this->bar()['labels'],
        ];
    }

    public function bar(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created 1',
                    'data' => [40, 100, 5, 26, 21, 32, 45, 74, 65, 45, 77, 89],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    public function bar2(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created 2',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Use 'line' or 'bar' based on your requirements
    }

    // protected function getOptions(): array
    // {
    //     return [
    //         'responsive' => true,  // Ensures the chart resizes responsively
    //         'maintainAspectRatio' => true, // Disables maintaining aspect ratio (if needed)
    //         'scales' => [
    //             'x' => [
    //                 'beginAtZero' => false, // Optional, depending on your needs
    //             ],
    //             'y' => [
    //                 'beginAtZero' => false, // Optional, depending on your needs
    //             ],
    //         ],
    //     ];
    // }

    protected function getStyles(): array
    {
        return [
            'chart' => [
                'width' => '100%',  // Set the width to 100% of the parent container
                'max-width' => '100%',  // Ensure it does not exceed the full width
                'height' => '400px',  // Set your desired height (increase this as needed)
            ],
        ];
    }
}
