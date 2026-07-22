@props(['poll'])

{{--
    Cédula de votação.

    Sem JavaScript, a cédula degrada para os selects nativos em `.ballot__fallback`.
    O script adiciona `is-enhanced` na raiz, esconde os selects, liga os botões de
    posição dos cards e passa a escrever em inputs hidden gerados por ele.
--}}
<form method="POST" action="{{ route('polls.vote', $poll) }}" class="ballot" data-ballot
      data-podium-size="{{ $poll->podium_size }}">
    @csrf

    <x-poll.podium-tracker :poll="$poll" />

    <p class="ballot__hint">
        Toque em um card para ocupar a próxima posição livre, ou use os botões
        {{ collect(range(1, $poll->podium_size))->map(fn ($p) => $p.'º')->join(', ', ' e ') }}
        para escolher a posição exata.
    </p>

    <div class="ballot__cards">
        @foreach ($poll->items as $item)
            <x-poll.item-card :item="$item" :poll="$poll" />
        @endforeach
    </div>

    <div class="ballot__fallback">
        @for ($position = 1; $position <= $poll->podium_size; $position++)
            <label for="position-{{ $position }}">
                {{ $position }}º lugar ({{ $poll->pointsForPosition($position) }} pts)
            </label>
            <select id="position-{{ $position }}" name="items[{{ $position }}]" required
                    data-fallback-select data-position="{{ $position }}">
                <option value="">— selecione —</option>
                @foreach ($poll->items as $item)
                    <option value="{{ $item->id }}" @selected(old("items.$position") == $item->id)>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        @endfor

        <x-ui.button type="submit" variant="primary">Enviar voto</x-ui.button>
    </div>

    <p class="sr-only" role="status" aria-live="polite" data-announcer></p>
</form>
