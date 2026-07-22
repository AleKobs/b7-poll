@props(['poll'])

@php($tones = [1 => 'gold', 2 => 'silver', 3 => 'bronze'])
@php($defaultTone = 'plain')

<div class="podium-tracker" data-tracker>
    <div class="podium-tracker__head">
        <span class="podium-tracker__kicker">Seu pódio</span>
        <span class="podium-tracker__progress" data-progress>0 de {{ $poll->podium_size }}</span>
    </div>

    <ol class="podium-slots">
        @for ($position = 1; $position <= $poll->podium_size; $position++)
            <li class="podium-slot" data-slot="{{ $position }}" data-tone="{{ $tones[$position] ?? $defaultTone }}"
                data-points="{{ $poll->pointsForPosition($position) }}">
                <span class="podium-slot__rank">{{ $position }}º</span>
                <span class="podium-slot__name" data-slot-name>Vazio</span>
                <span class="podium-slot__points">{{ $poll->pointsForPosition($position) }} pts</span>
                <button type="button" class="podium-slot__clear focus-ring" data-clear hidden
                        aria-label="Remover item do {{ $position }}º lugar">×</button>
            </li>
        @endfor
    </ol>

    <div class="podium-tracker__foot">
        <span class="podium-tracker__total" data-total>0 pts distribuídos</span>
        <x-ui.button type="submit" variant="primary" data-submit disabled>
            Escolha {{ $poll->podium_size }} itens
        </x-ui.button>
    </div>
</div>
