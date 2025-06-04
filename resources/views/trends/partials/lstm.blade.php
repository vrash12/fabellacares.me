{{-- resources/views/trends/partials/lstm.blade.php --}}
<div class="card mb-4 shadow-sm">
  <div class="card-header bg-info text-white fw-semibold">
    How this LSTM forecast was produced
  </div>
  <div class="card-body">
    <ol class="small mb-4">
      <li><strong>Load data</strong> – daily token counts from <span class="text-nowrap">{{ $from }}</span> to <span class="text-nowrap">{{ $to }}</span>.</li>
      <li><strong>Scaling</strong> – values normalized to [0,1] via MinMaxScaler.</li>
      <li><strong>Windowed training</strong> – each training sample uses the previous {{ $windowSize }} days to predict the next day.</li>
      <li><strong>LSTM Fit</strong> – 50-unit LSTM trained with early stopping on that windowed data.</li>
      <li><strong>Forecast</strong> – after training, recursively predict {{ $steps }} days ahead (see plot below).</li>
    </ol>

    <canvas id="lstmChart" height="120"></canvas>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('lstmChart').getContext('2d');

      const labels = @json($trend['lstm']['dates']);
      const dataValues = @json($trend['lstm']['values']);

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'LSTM {{ $steps }}-day Forecast',
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
