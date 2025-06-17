{{-- resources/views/trends/partials/ensemble.blade.php --}}
<div class="card mb-4 shadow-sm">
  <div class="card-header bg-info text-white fw-semibold">
    How this Ensemble forecast was produced
  </div>
  <div class="card-body">
    <ol class="small mb-4">
      <li><strong>ARIMA component</strong> – trained on a 60-day window with order <code>(1,1,1)</code>, forecasting {{ $steps }} days.</li>
      <li><strong>LSTM component</strong> – trained on the previous {{ $windowSize }} days, forecasting {{ $steps }} days.</li>
      <li><strong>Ensemble</strong> – element-wise average of ARIMA + LSTM predictions.</li>
      <li><strong>Historical mean</strong> – average daily count over the training window.</li>
      <li><strong>Final forecast</strong> – combined series plotted below.</li>
    </ol>

    <canvas id="ensembleChart" height="120"></canvas>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('ensembleChart').getContext('2d');

      const labels = @json($trend['ensemble']['dates']);
      const dataValues = @json($trend['ensemble']['values']);

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Ensemble {{ $steps }}-day Forecast',
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
