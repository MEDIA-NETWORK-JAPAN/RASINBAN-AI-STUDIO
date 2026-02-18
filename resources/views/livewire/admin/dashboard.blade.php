<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.kpi-card
            label="契約拠点数"
            :value="$teamsCount"
            icon="fa-building"
            color="indigo"
        />
        <x-ui.kpi-card
            label="総リクエスト"
            :value="number_format($totalRequests)"
            icon="fa-exchange-alt"
            color="blue"
        />
        <x-ui.kpi-card
            label="稼働アプリ"
            :value="$activeAppsCount"
            icon="fa-robot"
            color="green"
        />
    </div>
</div>
