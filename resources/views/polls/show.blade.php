@php($isOpen = $poll->isOpen())

<x-layouts.premium :title="$poll->title">
    @push('scripts')
        <script src="{{ asset('assets/premium-home/ballot.js') }}" defer></script>
    @endpush

    <section class="page-heading page-heading--tight" aria-labelledby="page-title">
        <div>
            <a class="back-link focus-ring" href="{{ route('polls.index') }}">← Votações</a>

            <div class="status-row">
                @if ($isOpen)
                    <x-ui.pill tone="open">Aberta</x-ui.pill>
                @else
                    <x-ui.pill tone="finished">Finalizada</x-ui.pill>
                @endif
                <x-ui.pill icon="trophy">Pódio de {{ $poll->podium_size }}</x-ui.pill>
                <x-ui.pill icon="clock">
                    {{ $isOpen
                        ? 'Encerra '.$poll->expires_at->format('d/m \à\s H:i')
                        : 'Encerrada em '.$poll->expires_at->format('d/m \à\s H:i') }}
                </x-ui.pill>
            </div>

            <h1 id="page-title">{{ $poll->title }}</h1>

            @if ($poll->description)
                <p class="lead">{{ $poll->description }}</p>
            @endif
        </div>
    </section>

    @if ($isOpen && ! $alreadyVoted)
        @if ($errors->any())
            <x-ui.alert>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <x-poll.ballot :poll="$poll" />
    @else
        <div class="side-panel notice-panel">
            <img src="{{ asset('assets/premium-home/'.($alreadyVoted ? 'check' : 'results').'.svg') }}" alt="">
            <div>
                <h2>{{ $alreadyVoted ? 'Você já votou nesta votação' : 'Esta votação está finalizada' }}</h2>
                <p>
                    {{ $alreadyVoted
                        ? 'Cada participante vota uma vez por votação. Acompanhe o pódio ao vivo.'
                        : 'O período de votação terminou. Confira como ficou o pódio final.' }}
                </p>
                <div class="card-actions">
                    <x-ui.button variant="primary" :href="route('polls.results', $poll)">Ver resultados</x-ui.button>
                    <x-ui.button :href="route('polls.index')">Voltar às votações</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</x-layouts.premium>
