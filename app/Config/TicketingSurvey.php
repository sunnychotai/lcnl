<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Committee questionnaire on ticketing and event money, run before the
 * website's ticketing module is designed. Answers are stored as JSON in
 * survey_responses (survey = $key), so the wording, options and order below
 * can change without a migration. Removing an option leaves older answers
 * showing under "other / no longer listed" in the results.
 *
 * The page is unlisted: nothing links to it, it is noindex, and the slug is
 * hard to guess. Share the link with committee members directly.
 *
 * Question types:
 *   text      one-line answer
 *   textarea  free comment
 *   single    pick one (radio buttons)
 *   multi     tick all that apply; 'other' => true adds an "Other" text box
 *   scale     pick one, shown as a row of buttons
 *   grid      one pick per row, same columns for every row
 *
 * Overridable from .env with the `ticketingSurvey.*` prefix.
 */
class TicketingSurvey extends BaseConfig
{
    /** survey_responses.survey for this questionnaire. */
    public string $key = 'ticketing-2026';

    /** Public URL. Unguessable on purpose; change it to retire an old link. */
    public string $slug = 'feedback/ticketing-k4w9p';

    public string $title = 'Ticketing and events questionnaire';

    /** Last day answers are accepted (Europe/London), or '' to stay open. */
    public string $closingDate = '';

    public string $contactEmail = 'info@lcnl.org';

    /** Answers to the "how may we use your comments" question. */
    public const QUOTE_NAMED     = 'named';
    public const QUOTE_ANONYMOUS = 'anonymous';

    public function __construct()
    {
        parent::__construct();

        $this->slug        = trim((string) env('ticketingSurvey.slug', $this->slug), '/');
        $this->closingDate = (string) env('ticketingSurvey.closingDate', $this->closingDate);
    }

    public function isOpen(?\DateTimeInterface $now = null): bool
    {
        if ($this->closingDate === '') {
            return true;
        }

        $now ??= new \DateTimeImmutable('now', new \DateTimeZone('Europe/London'));

        return $now <= new \DateTimeImmutable($this->closingDate . ' 23:59:59', new \DateTimeZone('Europe/London'));
    }

    /**
     * Columns shared by every row of the requirements grid, in display order.
     * The score weights rank features in the results (must = 2, nice = 1).
     */
    public array $gridColumns = [
        'must'   => ['label' => 'Must have', 'score' => 2],
        'nice'   => ['label' => 'Nice to have', 'score' => 1],
        'no'     => ['label' => 'Not needed', 'score' => 0],
        'unsure' => ['label' => 'Not sure', 'score' => null],
    ];

    /**
     * The questionnaire: sections in order, each with its questions.
     * Question ids are stored with every answer, so never reuse one for a
     * different question.
     */
    public function sections(): array
    {
        return [
            [
                'id'    => 'you',
                'title' => 'About you',
                'intro' => 'So we know which committees and roles each answer comes from.',
                'questions' => [
                    'name' => [
                        'type'         => 'text',
                        'label'        => 'Your name',
                        'required'     => true,
                        'max'          => 120,
                        'autocomplete' => 'name',
                    ],
                    'committees' => [
                        'type'     => 'multi',
                        'label'    => 'Which committee(s) are you on?',
                        'required' => true,
                        'options'  => [
                            'executive' => 'Executive Committee',
                            'mahila'    => 'Mahila Committee',
                            'yls'       => 'Young Lohana Society',
                            'lyc'       => 'Lohana Youth Club',
                            'lcf'       => 'Lohana Charitable Foundation',
                            'subcttee'  => 'An event sub-committee or working group',
                        ],
                        'other' => true,
                    ],
                    'roles' => [
                        'type'    => 'multi',
                        'label'   => 'What do you usually do at ticketed events?',
                        'help'    => 'Tick all that apply.',
                        'options' => [
                            'organise' => 'Organise or lead the event',
                            'sell'     => 'Sell tickets or take bookings',
                            'cash'     => 'Collect cash or chase payments',
                            'treasury' => 'Treasurer, banking or accounts',
                            'door'     => 'Work the door or check people in',
                            'numbers'  => 'Seating, catering or venue numbers',
                            'promote'  => 'Promote the event (WhatsApp, social media, fliers)',
                            'none'     => 'I am not usually involved with tickets',
                        ],
                    ],
                    'email' => [
                        'type'         => 'text',
                        'input'        => 'email',
                        'label'        => 'Email address',
                        'help'         => 'Optional. Only so we can follow up on your answers or ask you to try the new system.',
                        'max'          => 255,
                        'autocomplete' => 'email',
                    ],
                ],
            ],

            [
                'id'    => 'today',
                'title' => 'How ticketing works today',
                'intro' => 'Please describe what actually happens, including the workarounds.',
                'questions' => [
                    'event_types' => [
                        'type'    => 'multi',
                        'label'   => 'Which kinds of events have you helped sell tickets or take bookings for in the last two years?',
                        'options' => [
                            'large'    => 'Large ticketed events (e.g. Navratri, Christmas Mehfil, dinner and dance)',
                            'small'    => 'Smaller paid events (talks, trips, classes, socials)',
                            'religious'=> 'Religious or cultural bookings (e.g. Chopda Pujan)',
                            'sport'    => 'Sports and tournaments (e.g. golf, table tennis)',
                            'free'     => 'Free events where we need numbers (RSVP)',
                            'fund'     => 'Fundraisers and raffles',
                        ],
                        'other' => true,
                    ],
                    'buy_channels' => [
                        'type'    => 'multi',
                        'label'   => 'How do people buy tickets from LCNL today?',
                        'help'    => 'Tick every route you have seen used.',
                        'options' => [
                            'cash_member'  => 'Cash handed to a committee member',
                            'bank'         => 'Bank transfer to LCNL',
                            'card_site'    => 'Card payment on the LCNL website',
                            'message'      => 'WhatsApp, text or phone message to an organiser',
                            'paper'        => 'Paper tickets or a ticket book',
                            'door'         => 'Paying on the door on the night',
                            'thirdparty'   => 'Another website (e.g. Eventbrite)',
                        ],
                        'other' => true,
                    ],
                    'tracking' => [
                        'type'    => 'multi',
                        'label'   => 'Where are bookings and payments recorded today?',
                        'options' => [
                            'paper'       => 'Paper list or notebook',
                            'own_sheet'   => 'Someone’s own spreadsheet',
                            'shared_sheet'=> 'A shared spreadsheet (Google Sheets, Excel online)',
                            'whatsapp'    => 'WhatsApp chats',
                            'website'     => 'The LCNL website admin',
                            'memory'      => 'Mostly in people’s heads',
                        ],
                        'other' => true,
                    ],
                    'current_rating' => [
                        'type'    => 'scale',
                        'label'   => 'Overall, how well does the current way of selling tickets work?',
                        'options' => [
                            '1' => 'Very badly',
                            '2' => 'Badly',
                            '3' => 'OK',
                            '4' => 'Well',
                            '5' => 'Very well',
                            'na' => 'Can’t say',
                        ],
                    ],
                    'pain_points' => [
                        'type'    => 'multi',
                        'label'   => 'Which of these cause real problems today?',
                        'help'    => 'Tick all that apply. Leave out anything that is only a minor annoyance.',
                        'options' => [
                            'chasing'     => 'Chasing people who have not paid',
                            'matching'    => 'Matching bank transfers to bookings',
                            'cash'        => 'Handling, counting and banking cash',
                            'how_many'    => 'Not knowing how many tickets have sold',
                            'overselling' => 'Overselling, or double bookings',
                            'seating'     => 'Table and seating plans',
                            'changes'     => 'Changes, cancellations and refunds',
                            'door'        => 'Queues or confusion at the door',
                            'numbers'     => 'Getting final numbers to the caterer or venue',
                            'reconcile'   => 'Squaring the money for the treasurer afterwards',
                            'key_person'  => 'Too much depends on one or two people',
                            'how_to_book' => 'People not knowing how or where to book',
                        ],
                        'other' => true,
                    ],
                    'biggest_headache' => [
                        'type'  => 'textarea',
                        'label' => 'What is the single biggest headache, and can you give a real example of when it happened?',
                        'help'  => 'A specific story is more useful to us than a general comment.',
                    ],
                    'volunteer_hours' => [
                        'type'    => 'single',
                        'label'   => 'For one large event, roughly how many volunteer hours go on tickets and payments, everyone combined?',
                        'help'    => 'Selling, chasing, recording, banking and reconciling. A rough guess is fine.',
                        'options' => [
                            'lt5'   => 'Under 5 hours',
                            '5_10'  => '5 to 10 hours',
                            '10_20' => '10 to 20 hours',
                            '20_40' => '20 to 40 hours',
                            'gt40'  => 'More than 40 hours',
                            'dk'    => 'Don’t know',
                        ],
                    ],
                ],
            ],

            [
                'id'    => 'online',
                'title' => 'Selling tickets online',
                'intro' => 'Your honest view, including doubts. A “no” here is as useful to us as a “yes”.',
                'questions' => [
                    'support' => [
                        'type'    => 'scale',
                        'label'   => 'How do you feel about LCNL selling most tickets online through its own website?',
                        'options' => [
                            'strong_oppose' => 'Strongly against',
                            'oppose'        => 'Against',
                            'neutral'       => 'Neutral',
                            'support'       => 'In favour',
                            'strong_support'=> 'Strongly in favour',
                            'unsure'        => 'Unsure',
                        ],
                    ],
                    'concerns' => [
                        'type'    => 'multi',
                        'label'   => 'What worries you about it?',
                        'help'    => 'Tick any that apply.',
                        'options' => [
                            'exclusion' => 'Older or less confident members being left out',
                            'fees'      => 'Card and platform fees eating into income',
                            'failure'   => 'The system or Wi-Fi failing on the night',
                            'data'      => 'Keeping people’s personal data safe (GDPR)',
                            'personal'  => 'Losing the personal touch of buying from someone you know',
                            'training'  => 'Volunteers needing training to use it',
                            'cost'      => 'The cost of building and running it',
                            'fraud'     => 'Fake, copied or shared tickets',
                            'control'   => 'Committees losing control over who gets tickets',
                            'none'      => 'No real concerns',
                        ],
                        'other' => true,
                    ],
                    'payment_methods' => [
                        'type'    => 'multi',
                        'label'   => 'Which ways to pay should LCNL accept?',
                        'options' => [
                            'card'       => 'Card online, including Apple Pay and Google Pay',
                            'bank'       => 'Bank transfer with a booking reference',
                            'cash'       => 'Cash to a committee member, recorded in the system',
                            'door_cash'  => 'Cash on the door',
                            'door_card'  => 'Card on the door (card reader)',
                        ],
                        'other' => true,
                    ],
                    'fees' => [
                        'type'    => 'single',
                        'label'   => 'Card payments cost LCNL roughly 1.5% plus 20p per payment. Who should pay that?',
                        'options' => [
                            'absorb'   => 'LCNL absorbs it',
                            'price_in' => 'Build it into the ticket price',
                            'separate' => 'Show a separate booking fee to the buyer',
                            'unsure'   => 'Not sure',
                        ],
                    ],
                    'offline' => [
                        'type'    => 'single',
                        'label'   => 'Should people still be able to book without going online?',
                        'options' => [
                            'always'     => 'Yes, always: some people will never book online',
                            'transition' => 'Yes, for a year or two while people get used to it',
                            'no'         => 'No, online only',
                            'unsure'     => 'Not sure',
                        ],
                    ],
                ],
            ],

            [
                'id'    => 'features',
                'title' => 'What the system must do',
                'intro' => 'This is the part that shapes the build most directly. Choose one answer for each row.',
                'questions' => [
                    'features' => [
                        'type'  => 'grid',
                        'label' => 'How important is each of these?',
                        'rows'  => [
                            'phone_booking'  => 'Book and pay in a couple of minutes on a phone',
                            'member_price'   => 'Different prices for members and non-members',
                            'early_bird'     => 'Early bird or other time-limited prices',
                            'groups'         => 'Group and table bookings',
                            'seat_choice'    => 'Buyers choose their own table or seats',
                            'guest_details'  => 'Guest names, meal choices or dietary needs per ticket',
                            'on_behalf'      => 'Committee members book on someone’s behalf (phone or cash)',
                            'eticket'        => 'E-ticket with a QR code by email or WhatsApp',
                            'checkin'        => 'Check people in at the door on a phone',
                            'live_sales'     => 'Live sales figures for organisers',
                            'reminders'      => 'Automatic payment reminders, and releasing unpaid bookings',
                            'refunds'        => 'Refunds, cancellations and passing a ticket to someone else',
                            'waitlist'       => 'A waiting list when an event sells out',
                            'finance_report' => 'A finance report for the treasurer, split by payment method',
                            'codes'          => 'Discount or complimentary codes (volunteers, sponsors)',
                            'donation'       => 'Adding a donation and Gift Aid when booking',
                            'rsvp'           => 'Free RSVPs with a cap for free events',
                        ],
                    ],
                    'features_missing' => [
                        'type'  => 'textarea',
                        'label' => 'Is anything missing from that list that you would need?',
                        'rows'  => 3,
                    ],
                    'refund_authority' => [
                        'type'    => 'single',
                        'label'   => 'Who should be able to refund or cancel a paid booking?',
                        'options' => [
                            'any_organiser' => 'Any organiser of that event',
                            'event_lead'    => 'Only the event lead',
                            'treasurer'     => 'Only the treasurer',
                            'two_people'    => 'Two people together (e.g. event lead and treasurer)',
                            'unsure'        => 'Not sure',
                        ],
                    ],
                    'after_event' => [
                        'type'    => 'multi',
                        'label'   => 'After an event, what do you or the treasurer need to see?',
                        'options' => [
                            'total'      => 'Total income',
                            'by_method'  => 'Income split by card, bank transfer and cash',
                            'by_type'    => 'Sales by ticket type (member, early bird, table…)',
                            'fees'       => 'Fees deducted',
                            'attendees'  => 'Who booked and who actually came',
                            'unpaid'     => 'Anything still unpaid',
                            'refunds'    => 'Refunds given',
                            'previous'   => 'Comparison with previous years',
                        ],
                        'other' => true,
                    ],
                ],
            ],

            [
                'id'    => 'elsewhere',
                'title' => 'What you have seen work elsewhere',
                'questions' => [
                    'platforms' => [
                        'type'    => 'multi',
                        'label'   => 'Which of these have you used, as an organiser or a buyer, and found good?',
                        'options' => [
                            'eventbrite'    => 'Eventbrite',
                            'tickettailor'  => 'Ticket Tailor',
                            'ticketsource'  => 'TicketSource',
                            'skiddle'       => 'Skiddle or Fatsoma',
                            'paylinks'      => 'Payment links (Stripe, SumUp, PayPal)',
                            'forms'         => 'Google Forms or Microsoft Forms',
                            'community'     => 'Another community or temple organisation’s own system',
                            'none'          => 'None of these',
                        ],
                        'other' => true,
                    ],
                    'platform_lessons' => [
                        'type'  => 'textarea',
                        'label' => 'What did it do well, or badly, that we should copy or avoid?',
                        'help'  => 'For example: how quick checkout was, how check-in worked, what buyers complained about.',
                    ],
                ],
            ],

            [
                'id'    => 'last',
                'title' => 'Last few questions',
                'questions' => [
                    'one_change' => [
                        'type'  => 'textarea',
                        'label' => 'If you could change one thing about how LCNL sells tickets, what would it be?',
                        'rows'  => 3,
                    ],
                    'tester' => [
                        'type'    => 'single',
                        'label'   => 'Would you help test the new system before it goes live?',
                        'options' => [
                            'yes'   => 'Yes',
                            'maybe' => 'Maybe, depending on timing',
                            'no'    => 'No thanks',
                        ],
                    ],
                    'anything_else' => [
                        'type'  => 'textarea',
                        'label' => 'Anything else we should know?',
                        'rows'  => 3,
                    ],
                    'quote' => [
                        'type'     => 'single',
                        'label'    => 'When we share a summary of the feedback, how may we use your comments?',
                        'help'     => 'The project team will see your name either way, so we can follow up.',
                        'required' => true,
                        'options'  => [
                            self::QUOTE_NAMED     => 'You may quote me by name',
                            self::QUOTE_ANONYMOUS => 'Quote me, but do not use my name',
                        ],
                    ],
                ],
            ],
        ];
    }

    /** Every question keyed by id, across all sections. */
    public function questions(): array
    {
        $all = [];
        foreach ($this->sections() as $section) {
            foreach ($section['questions'] as $id => $q) {
                $all[$id] = $q + ['section' => $section['id']];
                if ($q['type'] === 'grid') {
                    $all[$id]['columns'] = $this->gridColumns;
                }
            }
        }

        return $all;
    }
}
