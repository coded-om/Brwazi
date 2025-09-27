<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use App\Models\Admin;

class ReportPolicy
{
    /**
     * Determine if the user/admin can view the list of reports.
     */
    public function viewAny($actor): bool
    {
        return $this->isModerator($actor);
    }

    /** View single report */
    public function view($actor, Report $report): bool
    {
        return $this->isModerator($actor) || ($actor instanceof User && $report->reporter_id === $actor->id);
    }

    /** Create a report (any authenticated user) */
    public function create(?User $user): bool
    {
        return $user?->id !== null; // logged in user
    }

    /** Update (handle) report */
    public function update($actor, Report $report): bool
    {
        return $this->isModerator($actor);
    }

    /** Resolve/close (same as update) */
    public function resolve($actor, Report $report): bool
    {
        return $this->isModerator($actor);
    }

    /** Delete report (optional restriction) */
    public function delete($actor, Report $report): bool
    {
        return $this->isModerator($actor);
    }

    protected function isModerator($actor): bool
    {
        if ($actor instanceof Admin) {
            return true; // enhance later: roles/permissions
        }
        // If you later add roles to User you can check them here
        return false;
    }
}
