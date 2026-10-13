<?php
declare(strict_types=1);

/*
 * Lineup rules for a fantasy roster.
 * 15 slots: QB, RB1, RB2, WR1, WR2, TE, FLEX, BN1..BN7
 */

// Every slot label, in display order.
function all_slot_labels(): array
{
    return ['QB', 'RB1', 'RB2', 'WR1', 'WR2', 'TE', 'FLEX',
            'BN1', 'BN2', 'BN3', 'BN4', 'BN5', 'BN6', 'BN7'];
}

// Starters are everything that is not a bench slot.
function is_starter_slot(string $slot): bool
{
    return in_array($slot, all_slot_labels(), true) && !str_starts_with($slot, 'BN');
}

// Which positions each slot accepts. Bench accepts any position.
function allowed_positions(string $slot): array
{
    return match (true) {
        $slot === 'QB'                 => ['QB'],
        $slot === 'RB1', $slot === 'RB2' => ['RB'],
        $slot === 'WR1', $slot === 'WR2' => ['WR'],
        $slot === 'TE'                 => ['TE'],
        $slot === 'FLEX'               => ['RB', 'WR', 'TE'],
        str_starts_with($slot, 'BN') && in_array($slot, all_slot_labels(), true)
                                       => ['QB', 'RB', 'WR', 'TE'],
        default                        => [],   // unknown slot: nothing fits
    };
}

// Can a player of this position sit in this slot? A null position means an empty slot, which is always fine.
function can_fill_slot(string $slot, ?string $position): bool
{
    if ($position === null) {
        return in_array($slot, all_slot_labels(), true);
    }
    return in_array($position, allowed_positions($slot), true);
}

// Would swapping the contents of two slots leave both of them legal?
function can_swap(string $slotA, ?string $posA, string $slotB, ?string $posB): bool
{
    if ($slotA === $slotB) {
        return false;
    }
    // The player from A goes to B, and the player from B goes to A.
    return can_fill_slot($slotB, $posA) && can_fill_slot($slotA, $posB);
}

// Given the current lineup, list the slots a player in $fromSlot could legally swap with.
// $lineup is [slotLabel => position or null].
function swap_targets(string $fromSlot, array $lineup): array
{
    $targets = [];
    foreach ($lineup as $slot => $position) {
        if (can_swap($fromSlot, $lineup[$fromSlot] ?? null, $slot, $position)) {
            $targets[] = $slot;
        }
    }
    return $targets;
}