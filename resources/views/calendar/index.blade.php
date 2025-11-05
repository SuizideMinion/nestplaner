<x-app-layout>
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-2xl font-semibold mb-6">Familienkalender</h1>

        <div id="calendar"></div>
    </div>

    {{-- FullCalendar --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek', // 👉 zeigt Woche mit Uhrzeiten
                locale: 'de',
                timeZone: 'local',
                selectable: true,
                editable: true,
                nowIndicator: true,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                slotDuration: '00:30:00', // 30-Minuten-Intervalle
                allDaySlot: true,
                events: '{{ route('calendar.fetch') }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                select: function(info) {
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

                eventDrop: function(info) {
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

                eventResize: function(info) {
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

                eventClick: function(info) {
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
