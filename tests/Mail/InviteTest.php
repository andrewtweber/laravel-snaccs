<?php

namespace Snaccs\Tests\Mail;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Snaccs\Mail\Invite;
use Snaccs\Mail\Schedulable;
use Snaccs\Tests\LaravelTestCase;

class InviteTest extends LaravelTestCase
{
    /**
     * @test
     */
    public function validate_attachment()
    {
        $event = $this->fakeEvent();

        $invite = new Invite($event, 'brucewayne@example.com');

        $this->assertSame('asdf.ics', $invite->filename());
        $this->assertSame('text/calendar;charset=UTF-8;method=REQUEST', $invite->mimetype());
    }

    /**
     * @test
     */
    public function validate_array()
    {
        Config::set('app.timezone', 'America/Chicago');
        Config::set('mail.from.name', 'Snaccs');
        Config::set('mail.from.address', 'snaccs@example.com');

        $event = $this->fakeEvent();

        $invite = new Invite($event, 'brucewayne@example.com');

        $data = $invite->toArray();

        $this->assertSame('asdf', $data['uid']);
        $this->assertSame('Test Event', $data['title']);
        $this->assertSame('20200101T143000', $data['start']);
        $this->assertSame('20200101T163000', $data['end']);
        $this->assertSame(120, $data['duration']);
        $this->assertSame('America/Chicago', $data['timezone']);
        $this->assertSame('123 Ohio St, Chicago, IL 60601', $data['location']);
        $this->assertSame('Snaccs', $data['organizer']);
        $this->assertSame('snaccs@example.com', $data['organizerEmail']);
        $this->assertSame('brucewayne@example.com', $data['attendee']);
    }

    /**
     * @test
     */
    public function validate_string()
    {
        Config::set('app.name', 'Test');
        Config::set('app.timezone', 'America/Chicago');
        Config::set('mail.from.name', 'Snaccs');
        Config::set('mail.from.address', 'snaccs@example.com');

        $event = $this->fakeEvent();

        $now = Carbon::parse("2019-12-31 08:15:00");
        $invite = new Invite($event, 'brucewayne@example.com', $now);

        $expected = <<<EXPECTED
            BEGIN:VCALENDAR
            METHOD:REQUEST
            VERSION:2.0
            PRODID:-//GCFA.com//Test//EN
            BEGIN:VEVENT
            UID:asdf
            ORGANIZER;CN="Snaccs":mailto:snaccs@example.com
            ATTENDEE:mailto:brucewayne@example.com
            TZID:America/Chicago
            DTSTAMP:20191231T081500
            DTSTART:20200101T143000
            DTEND:20200101T163000
            LOCATION:123 Ohio St, Chicago, IL 60601
            SUMMARY:Test Event
            DESCRIPTION:Test Event
            END:VEVENT
            END:VCALENDAR
            EXPECTED;

        $this->assertSame($expected, (string)$invite);
        $this->assertSame($expected, $invite->contents());
    }

    /**
     * @test
     */
    public function validate_string_with_reminder()
    {
        Config::set('app.name', 'Test');
        Config::set('app.timezone', 'America/Chicago');
        Config::set('mail.from.name', 'Snaccs');
        Config::set('mail.from.address', 'snaccs@example.com');

        $event = $this->fakeEvent();

        $now = Carbon::parse("2019-12-31 08:15:00");
        $invite = new Invite($event, 'brucewayne@example.com', $now);
        $invite->addReminder(45);

        $expected = <<<EXPECTED
            BEGIN:VCALENDAR
            METHOD:REQUEST
            VERSION:2.0
            PRODID:-//GCFA.com//Test//EN
            BEGIN:VEVENT
            UID:asdf
            ORGANIZER;CN="Snaccs":mailto:snaccs@example.com
            ATTENDEE:mailto:brucewayne@example.com
            TZID:America/Chicago
            DTSTAMP:20191231T081500
            DTSTART:20200101T143000
            DTEND:20200101T163000
            LOCATION:123 Ohio St, Chicago, IL 60601
            SUMMARY:Test Event
            DESCRIPTION:Test Event
            BEGIN:VALARM
            TRIGGER:-PT45M
            ACTION:DISPLAY
            DESCRIPTION:Reminder
            END:VALARM
            END:VEVENT
            END:VCALENDAR
            EXPECTED;

        $this->assertSame($expected, (string)$invite);
        $this->assertSame($expected, $invite->contents());
    }

    /**
     * @test
     */
    public function validate_string_with_multiple_reminders()
    {
        Config::set('app.name', 'Test');
        Config::set('app.timezone', 'America/Chicago');
        Config::set('mail.from.name', 'Snaccs');
        Config::set('mail.from.address', 'snaccs@example.com');

        $event = $this->fakeEvent();

        $now = Carbon::parse("2019-12-31 08:15:00");
        $invite = new Invite($event, 'brucewayne@example.com', $now);
        $invite->addReminder(60);
        $invite->addReminder(2880);

        $expected = <<<EXPECTED
            BEGIN:VCALENDAR
            METHOD:REQUEST
            VERSION:2.0
            PRODID:-//GCFA.com//Test//EN
            BEGIN:VEVENT
            UID:asdf
            ORGANIZER;CN="Snaccs":mailto:snaccs@example.com
            ATTENDEE:mailto:brucewayne@example.com
            TZID:America/Chicago
            DTSTAMP:20191231T081500
            DTSTART:20200101T143000
            DTEND:20200101T163000
            LOCATION:123 Ohio St, Chicago, IL 60601
            SUMMARY:Test Event
            DESCRIPTION:Test Event
            BEGIN:VALARM
            TRIGGER:-PT1H
            ACTION:DISPLAY
            DESCRIPTION:Reminder
            END:VALARM
            BEGIN:VALARM
            TRIGGER:-P2D
            ACTION:DISPLAY
            DESCRIPTION:Reminder
            END:VALARM
            END:VEVENT
            END:VCALENDAR
            EXPECTED;

        $this->assertSame($expected, (string)$invite);
        $this->assertSame($expected, $invite->contents());
    }

    /**
     * @test
     */
    public function validate_reminder_triggers()
    {
        $event = $this->fakeEvent();

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(45);
        $this->assertSame(["-PT45M"], $invite->reminders);

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(60);
        $this->assertSame(["-PT1H"], $invite->reminders);

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(75);
        $this->assertSame(["-PT75M"], $invite->reminders);

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(120);
        $this->assertSame(["-PT2H"], $invite->reminders);

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(1440);
        $this->assertSame(["-P1D"], $invite->reminders);

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(1500);
        $this->assertSame(["-PT25H"], $invite->reminders);

        $invite = new Invite($event, 'brucewayne@example.com');
        $invite->addReminder(2880);
        $this->assertSame(["-P2D"], $invite->reminders);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Reminder interval must be > 0");
        $invite->addReminder(0);
    }

    /**
     * @return Schedulable
     */
    protected function fakeEvent(): Schedulable
    {
        return new class implements Schedulable {
            public function uid(): string {
                return 'asdf';
            }

            public function title(): string {
                return 'Test Event';
            }

            public function date(): Carbon {
                return Carbon::parse("2020-01-01 14:30:00");
            }

            public function duration(): int {
                return 120;
            }

            public function location(): string {
                return '123 Ohio St, Chicago, IL 60601';
            }
        };
    }
}
