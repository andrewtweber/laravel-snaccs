<?php

namespace Snaccs\Mail;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Invite
 *
 * @package Snaccs\Mail
 */
class Invite extends Attachment implements Arrayable
{
    public array $reminders = [];

    /**
     * Invite constructor.
     *
     * @param Schedulable $event
     * @param string      $recipient
     * @param Carbon|null $now
     */
    public function __construct(
        public Schedulable $event,
        public string $recipient,
        public ?Carbon $now = null
    ) {
    }

    /**
     * @param int $minutes
     *
     * @return $this
     */
    public function addReminder(int $minutes)
    {
        assert($minutes > 0, new \InvalidArgumentException("Reminder interval must be > 0"));

        $trigger = "-PT{$minutes}M";
        if ($minutes % 1440 === 0) {
            $days = $minutes / 1440;
            $trigger = "-P{$days}D";
        } elseif ($minutes % 60 === 0) {
            $hours = $minutes / 60;
            $trigger = "-PT{$hours}H";
        }

        $this->reminders[] = $trigger;

        return $this;
    }

    /**
     * @return string
     */
    protected function alerts(): string
    {
        $alerts = "";

        foreach ($this->reminders as $trigger) {
            $alerts .= <<<ALERT
                \nBEGIN:VALARM
                TRIGGER:{$trigger}
                ACTION:DISPLAY
                DESCRIPTION:Reminder
                END:VALARM
                ALERT;
        }

        return $alerts;
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
        $now = ($this->now ?? Carbon::now())->format('Ymd\THis');

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
        DESCRIPTION:{$data['title']}{$this->alerts()}
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
