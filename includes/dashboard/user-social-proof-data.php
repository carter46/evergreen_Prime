<?php
/**
 * 100 unique social-proof toast messages for the user dashboard header.
 */
function user_dashboard_social_proof_messages(): array {
    static $messages = null;
    if ($messages !== null) {
        return $messages;
    }

    $firstNames = [
        'James', 'Sarah', 'Michael', 'Emma', 'David', 'Olivia', 'Robert', 'Sophia', 'William', 'Isabella',
        'Daniel', 'Mia', 'Christopher', 'Charlotte', 'Matthew', 'Amelia', 'Andrew', 'Harper', 'Joseph', 'Evelyn',
        'Ryan', 'Abigail', 'Nathan', 'Emily', 'Kevin', 'Elizabeth', 'Brian', 'Sofia', 'Jason', 'Avery',
        'Marcus', 'Chloe', 'Tyler', 'Grace', 'Brandon', 'Lily', 'Justin', 'Zoe', 'Eric', 'Hannah',
        'Aaron', 'Natalie', 'Samuel', 'Victoria', 'Benjamin', 'Audrey', 'Patrick', 'Brooklyn', 'Steven', 'Claire',
    ];
    $lastInitials = [
        'W.', 'M.', 'K.', 'R.', 'T.', 'L.', 'H.', 'B.', 'C.', 'S.', 'P.', 'G.', 'D.', 'F.', 'N.', 'J.', 'A.', 'V.', 'E.', 'O.',
    ];
    $cities = [
        'New York', 'London', 'Singapore', 'Dubai', 'Toronto', 'Sydney', 'Berlin', 'Paris', 'Tokyo', 'Hong Kong',
        'Los Angeles', 'Chicago', 'Miami', 'Boston', 'San Francisco', 'Amsterdam', 'Zurich', 'Geneva', 'Munich', 'Madrid',
        'Milan', 'Stockholm', 'Oslo', 'Copenhagen', 'Vienna', 'Brussels', 'Dublin', 'Austin', 'Seattle', 'Denver',
        'Atlanta', 'Houston', 'Phoenix', 'Dallas', 'Montreal', 'Vancouver', 'Melbourne', 'Brisbane', 'Auckland', 'Tel Aviv',
    ];
    $actions = [
        'just invested',
        'just deposited',
        'secured a position of',
        'activated a plan with',
        'added',
        'allocated',
        'funded their wallet with',
        'opened a new position worth',
        'reinvested',
        'topped up their account with',
        'committed',
        'placed a trade for',
        'expanded their portfolio with',
        'locked in',
        'started trading with',
    ];

    $messages = [];
    $seen = [];

    for ($i = 0; $i < 100; $i++) {
        $first = $firstNames[$i % count($firstNames)];
        $last = $lastInitials[($i * 7 + 3) % count($lastInitials)];
        $city = $cities[($i * 11 + 5) % count($cities)];
        $action = $actions[($i * 13 + 2) % count($actions)];
        $base = 850 + (($i * 1619 + 503) % 49150);
        $cents = ($i * 37) % 100;
        $amount = number_format($base + $cents / 100, $cents > 0 ? 2 : 0, '.', ',');

        $name = $first . ' ' . $last;
        $text = $name . ' from ' . $city . ' ' . $action . ' $' . $amount;
        $key = strtolower($text);
        if (isset($seen[$key])) {
            $text = $name . ' from ' . $city . ' ' . $action . ' $' . number_format($base + $i * 17, 0, '.', ',');
        }
        $seen[$key] = true;
        $messages[] = $text;
    }

    return $messages;
}
