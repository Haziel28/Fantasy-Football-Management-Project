<?php
declare(strict_types=1);

// Run from the project folder:  php tests/lineup_rules_test.php
require_once __DIR__ . '/../src/lineup_rules.php';
// Run code for tests, if the line says ok, the test passed; if it says FAIL, the test failed.  The exit code is 0 if all tests passed, 1 if any failed.
$failures = 0;

function check(string $name, bool $actual, bool $expected): void
{
    global $failures;
    if ($actual === $expected) {
        echo "ok    $name\n";
    } else {
        echo "FAIL  $name\n";
        $failures++;
    }
}

// Single-slot rules
check('QB can start at QB',          can_fill_slot('QB', 'QB'),     true);
check('RB cannot start at QB',       can_fill_slot('QB', 'RB'),     false);
check('RB fits RB1',                 can_fill_slot('RB1', 'RB'),    true);
check('WR cannot fill RB2',          can_fill_slot('RB2', 'WR'),    false);
check('TE fits TE',                  can_fill_slot('TE', 'TE'),     true);
check('RB fits FLEX',                can_fill_slot('FLEX', 'RB'),   true);
check('WR fits FLEX',                can_fill_slot('FLEX', 'WR'),   true);
check('TE fits FLEX',                can_fill_slot('FLEX', 'TE'),   true);
check('QB cannot fill FLEX',         can_fill_slot('FLEX', 'QB'),   false);
check('QB fits bench',               can_fill_slot('BN3', 'QB'),    true);
check('empty slot is always fine',   can_fill_slot('WR1', null),    true);
check('unknown slot rejects',        can_fill_slot('BN8', 'QB'),    false);

// Swaps
check('WR1 <-> FLEX (WR) is legal',  can_swap('WR1', 'WR', 'FLEX', 'WR'), true);
check('QB <-> bench RB is illegal',  can_swap('QB', 'QB', 'BN1', 'RB'),   false);
check('QB <-> bench QB is legal',    can_swap('QB', 'QB', 'BN1', 'QB'),   true);
check('move starter to empty bench', can_swap('RB1', 'RB', 'BN5', null),  true);
check('move bench WR into empty WR2',can_swap('BN2', 'WR', 'WR2', null),  true);
check('same slot is not a swap',     can_swap('QB', 'QB', 'QB', 'QB'),    false);

// Starter slots
check('QB is a starter slot',        is_starter_slot('QB'),   true);
check('FLEX is a starter slot',      is_starter_slot('FLEX'), true);
check('BN1 is not a starter slot',   is_starter_slot('BN1'),  false);
check('15th slot does not exist',    in_array('BN8', all_slot_labels(), true), false);

// Swap targets for a sample lineup
$lineup = [
    'QB' => 'QB', 'RB1' => 'RB', 'RB2' => null, 'WR1' => 'WR', 'WR2' => 'WR', 'TE' => 'TE', 'FLEX' => 'WR',
    'BN1' => 'QB', 'BN2' => 'RB', 'BN3' => 'RB', 'BN4' => 'WR', 'BN5' => null, 'BN6' => null, 'BN7' => null,
];
$targets = swap_targets('BN2', $lineup); // bench RB
check('bench RB can take RB1',       in_array('RB1', $targets, true),  true);
check('bench RB can fill empty RB2', in_array('RB2', $targets, true),  true);
check('bench RB can take FLEX',      in_array('FLEX', $targets, true), true);
check('bench RB cannot take QB',     in_array('QB', $targets, true),   false);
check('bench RB cannot take WR1',    in_array('WR1', $targets, true),  false);

echo $failures === 0 ? "\nAll checks passed.\n" : "\n$failures check(s) failed.\n";
exit($failures === 0 ? 0 : 1);