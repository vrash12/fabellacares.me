{{-- resources/views/trends/partials/arima.blade.php --}}
<div class="card mb-4 shadow-sm">
  <div class="card-header bg-info text-white fw-semibold">
    How this ARIMA forecast was produced
  </div>
  <div class="card-body">
    <ol class="small mb-4">
      <li><strong>Load data</strong> – daily token counts from <span class="text-nowrap">{{ $from }}</span> to <span class="text-nowrap">{{ $to }}</span>.</li>
      <li><strong>ARIMA order</strong> – fitted with (1,1,1) on the last 60 days.</li>
      <li><strong>Forecast</strong> – {\{ $steps }}-day prediction via ARIMA.</li>
      <li><strong>Historical mean</strong> – average over training window.</li>
      <li><strong>Final forecast</strong> – values plotted below.</li>
    </ol>

    <canvas id="arimaChart" height="120"></canvas>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('arimaChart').getContext('2d');

      const labels = @json($trend['arima']['dates']);
      const dataValues = @json($trend['arima']['values']);

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'ARIMA {{ $steps }}-day Forecast',
            data: dataValues,
            borderWidth: 2,
            fill: false,
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'Token visits / day' }
            },
            x: {
              title: { display: true, text: 'Date' }
            }
          },
          plugins: {
            legend: { display: false },
            tooltip: { mode: 'index' }
          }
        }
      });
    });
  </script>
@endpush
