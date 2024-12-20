<?php

use Livewire\Volt\Component;
use App\Models\Statistic;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    public array $statistics;
    public array $months = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $currentYear = now()->format('Y');
        $datasets = Statistic::query()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->groupBy('ip')
            ->get()
            ->keyBy('month');

        $data = [];
        foreach ($this->months as $month => $name) {
            $data[] = $datasets->get($month, 0) ?? 0;
        }

        $this->statistics = [
            'labels' => array_values($this->months),
            'datasets' => [
                [
                    'label' => 'Kunjungan',
                    'data' => $data,
                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1
                ]
            ]
        ];
    }
}; ?>

<div>
    <canvas class="w-full h-72" id="statistic">
        @json($statistics)
    </canvas>
</div>

@script
<script>
    if (window.Chart) {
        const ctx = document.getElementById('statistic').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: @json($statistics),
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
@endscript
