@props([
    'percentage' => 0,
])

@php
    $pct = (int) $percentage;
    $displayPct = $pct;
    $barWidth = min($pct, 100);

    if ($pct >= 100) {
        $textClass = 'text-red-600';
        $barClass  = 'bg-red-500';
    } elseif ($pct >= 90) {
        $textClass = 'text-red-600';
        $barClass  = 'bg-yellow-500';
    } else {
        $textClass = 'text-gray-600';
        $barClass  = 'bg-blue-500';
    }
@endphp

<div class="flex items-center gap-2">
    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
        <div
            class="h-2 rounded-full {{ $barClass }}"
            style="width: {{ $barWidth }}%"
        ></div>
    </div>
    <span class="text-xs font-medium {{ $textClass }} w-10 text-right">{{ $displayPct }}%</span>
</div>
