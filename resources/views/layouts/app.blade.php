<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FabellaCares – Admin')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ───────── Bootstrap 5.3 CSS & Icons ───────── --}}
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"
      rel="stylesheet">
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css"
      rel="stylesheet">

    {{-- ───────── Inter Font ───────── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet">

    {{-- ───────── (Optional) Select2 CSS ───────── --}}
    <link
      href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet">

    {{-- ───────── (Optional) DataTables CSS ───────── --}}
    <link
      href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css"
      rel="stylesheet">

    {{-- ───────── Your global, custom CSS variables and layout styles ───────── --}}
    <style>
        /* … any global CSS you already had … */
    </style>

    {{-- ───────── THIS is where Blade will “inject” all of your @push('styles') blocks ───────── --}}
    @stack('styles')
</head>
<body>
    {{-- ───────── Your existing Sidebar / Navbar markup goes here ───────── --}}
    <nav class="sidebar" id="sidebar">
      <!-- … sidebar contents … -->
    </nav>

    <main>
      {{-- ── Sticky header, global alerts, etc. ── --}}
      <div class="main-header">
        {{-- … your header code … --}}
      </div>

      {{-- ── Flash messages (success / error / validation) ── --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- ── Page Content from child views ── --}}
      <div class="container-fluid py-4">
        @yield('content')
      </div>
    </main>

    {{-- ───────── Core JS Libraries ───────── --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="{{ asset('js/ph-address-selector.js') }}"></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js">
    </script>
    <script
      src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
    </script>

    {{-- ───────── Any global inline JS (e.g. sidebar toggle) ───────── --}}
    <script>
      function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
      }
      // … more global JS …
    </script>

    {{-- ───────── THIS will “inject” any @push('scripts') from child views ───────── --}}
    @stack('scripts')
</body>
</html>
