
@component('mail::message')
# ¡Gracias por tu inscripción, {{ $user->name }}!

Has completado tu registro para el evento:

### {{ $event->title }}
- Fecha: {{ $event->start_time }}
- Tipo: {{ $event->type }}
- Monto: ${{ $event->amount }}

### Tu ticket:
**{{ $ticketNumber }}**

Guarda este ticket, ya que lo necesitarás para validar tu entrada.

¡Te esperamos!

Gracias,<br>
{{ config('app.name') }}
@endcomponent
