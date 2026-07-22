<x-layouts.premium title="Votações da Live">
    <section class="page-heading" aria-labelledby="page-title">
      <div>
        <p class="eyebrow">Encontro Semanal B7Web</p>
        <h1 id="page-title">Votações da Live</h1>
        <p class="lead">Vote nos projetos favoritos e acompanhe o pódio em tempo real.</p>
      </div>

      <div class="summary-strip" aria-label="Resumo das votações">
        <div class="metric">
          <strong>{{ $openPolls->count() }}</strong>
          <span>abertas</span>
        </div>
        <div class="metric">
          <strong>{{ $myVotesCount }}</strong>
          <span>{{ $myVotesCount == 1 ? 'voto enviado' : 'votos enviados' }}</span>
        </div>
        <div class="metric">
          <strong>{{ $finishedPolls->count() }}</strong>
          <span>finalizadas</span>
        </div>
      </div>
    </section>

    <div class="dashboard">
      <section aria-labelledby="open-title">
        <div class="section-head">
          <div>
            <h2 id="open-title">Em andamento</h2>
            <p>Escolha um item para cada posição do pódio.</p>
          </div>
        </div>

        <div class="poll-stack">
          @forelse ($openPolls as $poll)
            @php($voted = in_array($poll->id, $votedIds))
            @php($exp = $poll->expires_at)
            @php($when = $exp->isToday() ? 'hoje' : ($exp->isTomorrow() ? 'amanhã' : 'em '.$exp->format('d/m')))

            <article class="poll-card {{ $voted ? 'compact' : '' }}">
              <div>
                <div class="status-row">
                  @if ($voted)
                    <x-ui.pill tone="voted" icon="check">Você já votou</x-ui.pill>
                  @else
                    <x-ui.pill tone="open">Aberta</x-ui.pill>
                  @endif
                  <x-ui.pill icon="trophy">Pódio de {{ $poll->podium_size }}</x-ui.pill>
                </div>

                <h2 class="poll-title">{{ $poll->title }}</h2>
                <p class="poll-desc">{{ $poll->description ?: 'Escolha seu top '.$poll->podium_size.' entre os itens apresentados.' }}</p>

                <div class="meta-grid">
                  <div class="meta-item">
                    <img src="{{ asset('assets/premium-home/clock.svg') }}" alt="">
                    <span>Encerra {{ $when }} às {{ $exp->format('H:i') }}</span>
                  </div>
                  <div class="meta-item">
                    <img src="{{ asset('assets/premium-home/users.svg') }}" alt="">
                    <span>{{ $poll->votes_count }} {{ $poll->votes_count == 1 ? 'voto registrado' : 'votos registrados' }}</span>
                  </div>
                </div>

                <div class="card-actions">
                  @if ($voted)
                    <x-ui.button :href="route('polls.results', $poll)">Ver resultados</x-ui.button>
                  @else
                    <x-ui.button variant="primary" :href="route('polls.show', $poll)">Votar</x-ui.button>
                    <x-ui.button :href="route('polls.results', $poll)">Ver detalhes</x-ui.button>
                  @endif
                </div>
              </div>

              @unless ($voted)
                <aside class="podium-panel" aria-label="Prévia do pódio" data-poll-id="{{ $poll->id }}">
                  <div class="podium-kicker">
                    <span>Pódio atual</span>
                    <span>ao vivo</span>
                  </div>
                  <img src="{{ asset('assets/premium-home/podium.svg') }}" alt="">
                  <ol class="ranking-mini" id="ranking-{{ $poll->id }}">
                    @foreach ($previews[$poll->id] as $row)
                      <li>
                        <span class="rank">{{ $row['rank'] }}</span>
                        @if($row['item']->url)
                          <a href="{{ $row['item']->url }}" target="_blank" rel="noopener noreferrer">{{ $row['item']->name }}</a>
                        @else
                          <span>{{ $row['item']->name }}</span>
                        @endif
                        <span class="score">{{ $row['points'] }} pts</span>
                      </li>
                    @endforeach
                  </ol>
                </aside>
              @endunless
            </article>
          @empty
            <article class="poll-card compact">
              <p class="empty-note">Nenhuma votação em andamento no momento.</p>
            </article>
          @endforelse
        </div>
      </section>

      <aside class="side-column" aria-label="Resultados finalizados">
        <section class="side-panel" id="finalizadas">
          <div class="section-head">
            <div>
              <h2>Finalizadas</h2>
              <p>Pódios já apurados.</p>
            </div>
          </div>

          <div class="result-stack">
            @forelse ($finishedPolls as $poll)
              @php($icon = $loop->even ? 'trophy' : 'results')
              @php($label = $poll->expires_at->isPast()
                    ? 'Finalizada em '.$poll->expires_at->format('d/m \à\s H:i')
                    : 'Votação encerrada')
              <article class="result-card">
                <img src="{{ asset('assets/premium-home/'.$icon.'.svg') }}" alt="">
                <div>
                  <h3>{{ $poll->title }}</h3>
                  <p>{{ $label }}</p>
                  <a class="text-link focus-ring" href="{{ route('polls.results', $poll) }}">Ver resultados</a>
                </div>
              </article>
            @empty
              <p class="empty-note">Nenhuma votação finalizada ainda.</p>
            @endforelse
          </div>

          <p class="live-note"><strong>Transparência:</strong> resultados exibem pontos totais e contagem por posição.</p>
        </section>

        <section class="side-panel">
          <div class="section-head">
            <div>
              <h2>Seu voto</h2>
              <p>Última atividade</p>
            </div>
          </div>

          @if ($lastVote)
            <div class="result-card">
              <img src="{{ asset('assets/premium-home/check.svg') }}" alt="">
              <div>
                <h3>{{ $lastVote->poll->title }}</h3>
                <p>Voto recebido em {{ $lastVote->created_at->format('d/m \à\s H:i') }}</p>
                <a class="text-link focus-ring" href="{{ route('polls.results', $lastVote->poll) }}">Conferir pódio</a>
              </div>
            </div>
          @else
            <p class="empty-note">Você ainda não votou em nenhuma votação.</p>
          @endif
        </section>
      </aside>
    </div>

    @push('scripts')
    <script>
      // Atualização do pódio ao vivo.
      document.addEventListener('DOMContentLoaded', function() {
        const podiumPanels = document.querySelectorAll('.podium-panel[data-poll-id]');

        podiumPanels.forEach(panel => {
          const pollId = panel.getAttribute('data-poll-id');
          const rankingList = panel.querySelector('.ranking-mini');

          setInterval(async () => {
            try {
              const response = await fetch(`/polls/${pollId}/ranking`);
              const data = await response.json();

              rankingList.innerHTML = data.ranking.map(row => `
                <li>
                  <span class="rank">${row.rank}</span>
                  ${row.item.url ? `<a href="${row.item.url}" target="_blank" rel="noopener noreferrer">${row.item.name}</a>` : `<span>${row.item.name}</span>`}
                  <span class="score">${row.points} pts</span>
                </li>
              `).join('');

              const voteCountElement = panel.closest('.poll-card').querySelector('.meta-item:last-child span');
              if (voteCountElement) {
                const count = data.votes_count;
                voteCountElement.textContent = `${count} ${count === 1 ? 'voto registrado' : 'votos registrados'}`;
              }
            } catch (error) {
              console.error('Error fetching ranking:', error);
            }
          }, 5000);
        });
      });
    </script>
    @endpush
</x-layouts.premium>
