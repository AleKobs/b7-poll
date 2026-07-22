@props(['ranking'])

@php($tones = [1 => 'gold', 2 => 'silver', 3 => 'bronze', 4 => 'gray'])

<ol class="ranking" data-ranking>
    @foreach ($ranking as $row)
        <li class="ranking__row" data-tone="{{ $tones[$row['rank']] ?? 'plain' }}">
            <span class="ranking__rank">{{ $row['rank'] }}º</span>

            <span class="ranking__info">
                <span class="ranking__name">
                    @if ($row['item']->url)
                        <a class="focus-ring" href="{{ $row['item']->url }}" target="_blank" rel="noopener noreferrer">
                            {{ $row['item']->name }} ↗
                        </a>
                    @else
                        {{ $row['item']->name }}
                    @endif
                </span>
                @if ($row['item']->author)
                    <span class="ranking__author">por {{ $row['item']->author }}</span>
                @endif
                @if (! empty($row['counts']))
                    <span class="ranking__counts">
                        @foreach (collect($row['counts'])->sortKeys() as $position => $times)
                            {{ $position }}º × {{ $times }}@if (! $loop->last) · @endif
                        @endforeach
                    </span>
                @endif
            </span>

            <span class="ranking__points">{{ $row['points'] }}<small>pts</small></span>
        </li>
    @endforeach
</ol>
