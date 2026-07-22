@props([
    'title' => 'Votações',
    'current' => 'polls',
])

<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }} · B7Web Votações</title>
  <link rel="stylesheet" href="{{ asset('assets/premium-home/home.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/premium-home/vote.css') }}">
  @stack('styles')
</head>
<body>
  <header class="topbar">
    <div class="nav-shell">
      <a class="brand focus-ring" href="{{ route('polls.index') }}" aria-label="B7Web Votações">
        <img src="{{ asset('assets/premium-home/brand-mark.svg') }}" alt="">
        <span>B7Web Votações</span>
      </a>

      <nav class="nav-links" aria-label="Principal">
        <a href="{{ route('polls.index') }}" @if ($current === 'polls') aria-current="page" @endif>Votações</a>
        <a href="{{ route('polls.index') }}#finalizadas" @if ($current === 'results') aria-current="page" @endif>Resultados</a>
      </nav>

      <div class="user-actions">
        <span class="hello">Olá, {{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="icon-button focus-ring" type="submit" aria-label="Sair">
            <img src="{{ asset('assets/premium-home/logout.svg') }}" alt="">
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="shell">
    {{ $slot }}
  </main>

  @if (session('status'))
    <x-ui.toast tone="success">{{ session('status') }}</x-ui.toast>
  @endif

  <script src="{{ asset('assets/premium-home/ui.js') }}" defer></script>
  @stack('scripts')
</body>
</html>
