<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;

class SectionPolicy
{
    // Same rule as Section::scopeVisibleTo(). Change both together.
    public function view(User $user, Section $section): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Without this check, null === null would open sections with no adviser.
        return $user->teacher_id !== null
            && $section->adviser_id === $user->teacher_id;
    }
}
