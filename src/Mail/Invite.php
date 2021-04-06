<?php

namespace Snaccs\Mail;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Invite
 *
 * @package Snaccs\Mail
 */
class Invite extends Attachment implements Arrayable
{
    /**
     * Invite constructor.
     *
     * @param Schedulable $event
     * @param string      $recipient
     */
    public function __construct(
        public Schedulable $event,
        public string $recipient
    ) {
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $start = $this->event->date();
        $duration = $this->event->duration();
        $end = $start->copy()->addMinutes($duration);

        return [
            'uid'            => $this->event->uid(),
            'title'          => $this->event->title(),
            'start'          => $start->format('Ymd\THis'),
            'end'            => $end->format('Ymd\THis'),
            'duration'       => $duration,
            'timezone'       => config('app.timezone'),
            'location'       => $this->event->location(),
            'organizer'      => config('mail.from.name'),
            'organizerEmail' => config('mail.from.address'),
            'attendee'       => $this->recipient,
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $data = $this->toArray();

        $name = config('app.name');
        $now = date('Ymd') . 'T' . date('His');

        return <<<VCAL
        BEGIN:VCALENDAR
        METHOD:REQUEST
        VERSION:2.0
        PRODID:-//GCFA.com//{$name}//EN
        BEGIN:VEVENT
        UID:{$data['uid']}
        ORGANIZER;CN="{$data['organizer']}":mailto:{$data['organizerEmail']}
        ATTENDEE:mailto:{$data['attendee']}
        TZID:{$data['timezone']}
        DTSTAMP:{$now}
        DTSTART:{$data['start']}
        DTEND:{$data['end']}
        LOCATION:{$data['location']}
        SUMMARY:{$data['title']}
        DESCRIPTION:{$data['title']}
        BEGIN:VALARM
        TRIGGER:-PT60M
        ACTION:DISPLAY
        DESCRIPTION:Reminder
        END:VALARM
        END:VEVENT
        END:VCALENDAR
        VCAL;
    }

    /**
     * @return string
     */
    public function filename(): string
    {
        return $this->event->uid() . '.ics';
    }

    /**
     * @return string
     */
    public function contents(): string
    {
        return (string)$this;
    }

    /**
     * @return string
     */
    public function mimetype(): string
    {
        return 'text/calendar;charset=UTF-8;method=REQUEST';
    }
}
