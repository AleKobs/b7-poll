@php($isOpen = $poll->isOpen())

<x-layouts.premium :title="'Resultados · '.$poll->title" current="results">
    <section class="page-heading page-heading--tight" aria-labelledby="page-title">
        <div>
            <a class="back-link focus-ring" href="{{ route('polls.index') }}">← Votações</a>

            <div class="status-row">
                @if ($isOpen)
                    <x-ui.pill tone="open">Parcial ao vivo</x-ui.pill>
                @else
                    <x-ui.pill tone="finished">Resultado final</x-ui.pill>
                @endif
                <x-ui.pill icon="trophy">Pódio de {{ $poll->podium_size }}</x-ui.pill>
                <x-ui.pill icon="users">
                    {{ $votesCount }} {{ $votesCount == 1 ? 'voto' : 'votos' }}
                </x-ui.pill>
            </div>

            <h1 id="page-title">{{ $poll->title }}</h1>
            <p class="lead">
                {{ $isOpen
                    ? 'Pontuação recalculada a cada voto. Empates são resolvidos por número de primeiros lugares.'
                    : 'Votação encerrada em '.$poll->expires_at->format('d/m/Y \à\s H:i').'.' }}
            </p>
        </div>
    </section>

    <div class="side-panel results-panel">
        <div class="section-head">
            <div>
                <h2>Classificação</h2>
                <p>{{ $poll->podium_size }} primeiros sobem ao pódio.</p>
            </div>
            <x-ui.button data-share>Compartilhar</x-ui.button>
        </div>

        @if ($ranking->isEmpty())
            <p class="empty-note">Esta votação ainda não tem itens.</p>
        @else
            <x-poll.ranking-list :ranking="$ranking" />
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const trigger = document.querySelector('[data-share]');

                if (! trigger) {
                    return;
                }

                const lines = @json($ranking->take(3)->map(fn ($row) => $row['rank'].'º '.$row['item']->name.' — '.$row['points'].' pts')->values());
                const text = @json('Resultados: '.$poll->title)+'\n\n'+lines.join('\n');

                trigger.addEventListener('click', async () => {
                    const payload = { title: @json($poll->title), text, url: window.location.href };

                    if (navigator.share) {
                        try {
                            await navigator.share(payload);
                            return;
                        } catch (error) {
                            if (error.name === 'AbortError') {
                                return;
                            }
                        }
                    }

                    await navigator.clipboard.writeText(text+'\n'+window.location.href);
                    trigger.textContent = 'Copiado!';
                    setTimeout(() => { trigger.textContent = 'Compartilhar'; }, 2000);
                });
            });
        </script>
    @endpush
</x-layouts.premium>
