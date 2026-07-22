@props(['item', 'poll'])

<article class="item-card" data-item="{{ $item->id }}" data-item-name="{{ $item->name }}">
    <button type="button" class="item-card__pick focus-ring" data-pick
            aria-label="Escolher {{ $item->name }} para a próxima posição livre">
        <span class="item-card__medal" data-medal aria-hidden="true"></span>
        <span class="item-card__content">
            <span class="item-card__name">{{ $item->name }}</span>
            @if ($item->author)
                <span class="item-card__author">por {{ $item->author }}</span>
            @endif
            @if ($item->description)
                <span class="item-card__desc">{{ $item->description }}</span>
            @endif
        </span>
    </button>

    <div class="item-card__foot">
        <div class="item-card__positions" role="group" aria-label="Posição de {{ $item->name }} no pódio">
            @for ($position = 1; $position <= $poll->podium_size; $position++)
                <button type="button" class="position-button focus-ring" data-position="{{ $position }}"
                        aria-pressed="false"
                        aria-label="Colocar {{ $item->name }} em {{ $position }}º lugar">
                    {{ $position }}º
                </button>
            @endfor
        </div>

        @if ($item->url)
            <a class="text-link focus-ring" href="{{ $item->url }}" target="_blank" rel="noopener noreferrer">
                Ver projeto ↗
            </a>
        @endif
    </div>
</article>
