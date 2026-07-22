// Cédula de votação: seleção do pódio por toque sequencial + seletor de posição.
//
// Sem este script a cédula continua funcionando pelos selects nativos em
// `.ballot__fallback`. O script assume o controle marcando a raiz com
// `is-enhanced`, desativa os selects e passa a escrever em inputs hidden.
(function () {
    'use strict';

    var TONES = { 1: 'gold', 2: 'silver', 3: 'bronze', 4: 'gray' };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-ballot]').forEach(setupBallot);
    });

    function setupBallot(form) {
        var size = Number(form.dataset.podiumSize);
        var cards = Array.prototype.slice.call(form.querySelectorAll('.item-card'));
        var slots = Array.prototype.slice.call(form.querySelectorAll('.podium-slot'));
        var selects = Array.prototype.slice.call(form.querySelectorAll('[data-fallback-select]'));
        var submit = form.querySelector('[data-submit]');
        var progress = form.querySelector('[data-progress]');
        var total = form.querySelector('[data-total]');
        var announcer = form.querySelector('[data-announcer]');

        if (! size || ! cards.length) {
            return;
        }

        // position (number) => itemId (string)
        var picks = new Map();

        var inputs = document.createElement('div');
        inputs.hidden = true;
        form.appendChild(inputs);

        // Estado inicial vem dos selects, que já honram o old() do Laravel.
        selects.forEach(function (select) {
            if (select.value) {
                picks.set(Number(select.dataset.position), select.value);
            }
            // Desabilitar (em vez de esconder) evita que um campo required
            // invisível trave a submissão do formulário.
            select.disabled = true;
        });

        cards.forEach(function (card) {
            var itemId = card.dataset.item;

            card.querySelector('[data-pick]').addEventListener('click', function () {
                pickNext(itemId);
            });

            card.querySelectorAll('[data-position]').forEach(function (button) {
                button.addEventListener('click', function () {
                    assign(itemId, Number(button.dataset.position));
                });
            });
        });

        slots.forEach(function (slot) {
            slot.querySelector('[data-clear]').addEventListener('click', function () {
                var position = Number(slot.dataset.slot);
                var name = nameOf(picks.get(position));

                picks.delete(position);
                render();
                announce(name + ' saiu do ' + position + 'º lugar.');
            });
        });

        form.addEventListener('submit', function (event) {
            if (picks.size !== size) {
                event.preventDefault();
                announce('Escolha ' + size + ' itens antes de enviar.');

                return;
            }

            submit.disabled = true;
            submit.textContent = 'Enviando…';
        });

        render();
        form.classList.add('is-enhanced');

        /**
         * Coloca o item na posição pedida. Se o item já ocupava outra posição,
         * as duas trocam de lugar; senão, quem estava lá volta para o banco.
         */
        function assign(itemId, position) {
            var previous = positionOf(itemId);
            var occupant = picks.get(position) || null;

            if (previous === position) {
                picks.delete(position);
                render();
                announce(nameOf(itemId) + ' saiu do pódio.');

                return;
            }

            picks.set(position, itemId);

            if (previous !== null) {
                if (occupant) {
                    picks.set(previous, occupant);
                } else {
                    picks.delete(previous);
                }
            }

            render();
            announce(nameOf(itemId) + ' agora está em ' + position + 'º lugar.');
        }

        /** Ocupa a próxima posição livre, ou tira o item do pódio se já estiver nele. */
        function pickNext(itemId) {
            var previous = positionOf(itemId);

            if (previous !== null) {
                picks.delete(previous);
                render();
                announce(nameOf(itemId) + ' saiu do pódio.');

                return;
            }

            for (var position = 1; position <= size; position++) {
                if (! picks.has(position)) {
                    assign(itemId, position);

                    return;
                }
            }

            announce('O pódio já está completo. Use os botões de posição para trocar.');
        }

        function positionOf(itemId) {
            var found = null;

            picks.forEach(function (value, position) {
                if (value === itemId) {
                    found = position;
                }
            });

            return found;
        }

        function nameOf(itemId) {
            var card = cards.find(function (candidate) {
                return candidate.dataset.item === itemId;
            });

            return card ? card.dataset.itemName : 'Item';
        }

        function render() {
            var points = 0;

            slots.forEach(function (slot) {
                var position = Number(slot.dataset.slot);
                var itemId = picks.get(position);
                var clear = slot.querySelector('[data-clear]');

                slot.querySelector('[data-slot-name]').textContent = itemId ? nameOf(itemId) : 'Vazio';
                clear.hidden = ! itemId;

                if (itemId) {
                    slot.dataset.filled = '';
                    clear.setAttribute('aria-label', 'Remover ' + nameOf(itemId) + ' do ' + position + 'º lugar');
                    points += Number(slot.dataset.points);
                } else {
                    delete slot.dataset.filled;
                }
            });

            cards.forEach(function (card) {
                var position = positionOf(card.dataset.item);
                var medal = card.querySelector('[data-medal]');
                var pick = card.querySelector('[data-pick]');

                if (position === null) {
                    delete card.dataset.picked;
                    delete card.dataset.tone;
                    medal.textContent = '';
                    pick.setAttribute(
                        'aria-label',
                        'Escolher ' + card.dataset.itemName + ' para a próxima posição livre'
                    );
                } else {
                    card.dataset.picked = '';
                    card.dataset.tone = TONES[position] || 'plain';
                    medal.textContent = position + 'º';
                    pick.setAttribute(
                        'aria-label',
                        card.dataset.itemName + ' está em ' + position + 'º lugar. Remover do pódio'
                    );
                }

                card.querySelectorAll('[data-position]').forEach(function (button) {
                    button.setAttribute(
                        'aria-pressed',
                        Number(button.dataset.position) === position ? 'true' : 'false'
                    );
                });
            });

            var missing = size - picks.size;

            progress.textContent = picks.size + ' de ' + size;
            total.textContent = points + ' pts distribuídos';
            submit.disabled = missing > 0;
            submit.textContent = missing > 0
                ? 'Escolha mais ' + missing + (missing === 1 ? ' item' : ' itens')
                : 'Enviar voto';

            inputs.innerHTML = '';
            picks.forEach(function (itemId, position) {
                var input = document.createElement('input');

                input.type = 'hidden';
                input.name = 'items[' + position + ']';
                input.value = itemId;
                inputs.appendChild(input);
            });
        }

        function announce(message) {
            if (announcer) {
                announcer.textContent = message;
            }
        }
    }
})();
