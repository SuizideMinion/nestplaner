<x-app-layout>
    <div class="container mx-auto py-8 px-4">
        <div class="d-flex w-100 justify-content-between">
            <h1 class="text-2xl font-semibold mb-6">Familienkalender</h1>

            <!-- Button zum Öffnen des Modals -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
                <i class="bi bi-plus-circle"></i> Termin hinzufügen
            </button>
        </div>
        <div id="calendar"></div>
    </div>

    <!-- Modal: Termin hinzufügen -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalLabel">Neuen Termin hinzufügen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>
                <div class="modal-body">
                    <form id="addEventForm">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Titel</label>
                            <input type="text" class="form-control" id="eventTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventStart" class="form-label">Startzeit</label>
                            <input type="datetime-local" class="form-control" id="eventStart" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventEnd" class="form-label">Endzeit</label>
                            <input type="datetime-local" class="form-control" id="eventEnd">
                        </div>
                        <div class="mb-3">
                            <label for="eventColor" class="form-label">Farbe (optional)</label>
                            <input type="color" class="form-control form-control-color" id="eventColor" value="#3788d8">
                        </div>
                        <div class="mb-3">
                            <label for="eventRecurring" class="form-label">Wiederholung</label>
                            <select class="form-select" id="eventRecurring">
                                <option value="none" selected>Keine</option>
                                <option value="daily">Täglich</option>
                                <option value="weekly">Wöchentlich</option>
                                <option value="monthly">Monatlich</option>
                                <option value="yearly">Jährlich</option>
                            </select>
                        </div>

                        <div class="mb-3 recurrence-date d-none">
                            <label for="eventRecurrenceDate" class="form-label">Wiederholung bis (optional)</label>
                            <input type="date" class="form-control" id="eventRecurrenceDate">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Speichern</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        // Dropdown-Änderung überwachen
        // Zeigt das "Wiederholung bis"-Datum nur bei Wiederholungen an
        document.getElementById('eventRecurring').addEventListener('change', function() {
            const dateContainer = document.querySelector('.recurrence-date');
            if (this.value === 'none') {
                dateContainer.classList.add('d-none');
            } else {
                dateContainer.classList.remove('d-none');
            }
        });

        // Wenn das Modal geöffnet wird → aktuelle Zeit eintragen
        document.getElementById('addEventModal').addEventListener('show.bs.modal', function () {
            const now = new Date();

            // Minuten auf nächste Viertelstunde runden
            const roundedMinutes = Math.ceil(now.getMinutes() / 15) * 15;
            now.setMinutes(roundedMinutes);
            now.setSeconds(0);

            const end = new Date(now.getTime() + 60 * 60 * 1000); // +1 Stunde

            // In ISO-Format für datetime-local umwandeln
            const toLocalInput = (d) => {
                const offset = d.getTimezoneOffset();
                const local = new Date(d.getTime() - offset * 60 * 1000);
                return local.toISOString().slice(0, 16);
            };

            document.getElementById('eventStart').value = toLocalInput(now);
            document.getElementById('eventEnd').value = toLocalInput(end);
        });

        document.getElementById('addEventForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const title = document.getElementById('eventTitle').value;
            const start = document.getElementById('eventStart').value;
            const end = document.getElementById('eventEnd').value;
            const color = document.getElementById('eventColor').value;
            const recurrence_type = document.getElementById('eventRecurring').value;
            const recurrence_date = document.getElementById('eventRecurrenceDate').value;

            if (!title || !start) {
                alert('Bitte mindestens Titel und Startzeit eingeben.');
                return;
            }

            try {
                const response = await fetch('{{ route('calendar.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        title,
                        start,
                        end,
                        color,
                        is_recurring: recurrence_type !== 'none',
                        recurrence_type: recurrence_type === 'none' ? null : recurrence_type,
                        recurrence_date: recurrence_date || null,
                    }),
                });

                if (!response.ok) throw new Error('Fehler beim Speichern.');

                calendar.refetchEvents(); // ✅ funktioniert jetzt, da global
                const modal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
                modal.hide();
                document.getElementById('addEventForm').reset();
            } catch (err) {
                console.error(err);
                alert('Fehler beim Erstellen des Termins.');
            }
        });


        let calendar; // 👈 global definiert

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 768 ? 'listWeek' : 'timeGridWeek',
                locale: 'de',
                timeZone: 'local',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: window.innerWidth < 768 ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                contentHeight: 'auto',
                expandRows: true,
                nowIndicator: true,
                slotMinTime: "06:00:00",
                slotMaxTime: "23:00:00",
                dayMaxEvents: true,
                eventDisplay: 'block',
                events: '{{ route('calendar.fetch') }}',

                select: function (info) {
                    const title = prompt('Titel des Termins:');
                    if (title) {
                        fetch('{{ route('calendar.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                title: title,
                                start: info.startStr,
                                end: info.endStr,
                                all_day: info.allDay,
                            }),
                        }).then(() => calendar.refetchEvents());
                    }
                },

                eventDrop: function (info) {
                    fetch(`/calendar/events/${info.event.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            start: info.event.startStr,
                            end: info.event.endStr,
                        }),
                    });
                },

                eventResize: function (info) {
                    fetch(`/calendar/events/${info.event.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            start: info.event.startStr,
                            end: info.event.endStr,
                        }),
                    });
                },

                eventClick: function (info) {
                    if (confirm(`Termin "${info.event.title}" löschen?`)) {
                        fetch(`/calendar/events/${info.event.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        }).then(() => calendar.refetchEvents());
                    }
                }
            });

            calendar.render();
        });
    </script>

</x-app-layout>
