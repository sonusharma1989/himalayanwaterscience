<?php

namespace Hws\FieldService\Helpers;

use Illuminate\Support\Facades\DB;

class BranchScopeHelper
{
    /**
     * Get current logged in admin user
     */
    public static function getCurrentAdmin()
    {
        return auth()->guard('admin')->user();
    }

    /**
     * Check if the current admin is assigned to a Head Office branch (is_head_office = 1)
     * ONLY Head Office branch assignment gives multi-branch access / switcher.
     */
    public static function isHeadOfficeUser(): bool
    {
        $admin = self::getCurrentAdmin();
        if (!$admin || empty($admin->branch_id)) {
            return false;
        }

        $branch = DB::table('hws_branches')->where('id', $admin->branch_id)->where('status', 1)->first();
        return $branch && (int)$branch->is_head_office === 1;
    }

    /**
     * Backward-compatible alias for isHeadOfficeUser
     */
    public static function isSuperAdmin(): bool
    {
        return self::isHeadOfficeUser();
    }

    /**
     * Get the active branch ID to filter by.
     * - Head Office user + All Branches selected => returns null (no filter = all data)
     * - Head Office user + Specific branch selected => returns that branch_id
     * - Normal branch user => always returns their own admins.branch_id (session ignored)
     * - Admin without branch_id => returns -1 (meaning no allowed branch, query returns zero records)
     */
    public static function getActiveBranchId(): ?int
    {
        $admin = self::getCurrentAdmin();
        if (!$admin || empty($admin->branch_id)) {
            return -1; // Blocked: user without branch_id cannot view any data
        }

        if (self::isHeadOfficeUser()) {
            $selected = session('hws_active_branch_id');
            if (!empty($selected) && $selected !== 'all') {
                return (int) $selected;
            }
            return null; // All Branches view
        }

        return (int) $admin->branch_id;
    }

    /**
     * Check if the logged-in admin can access a specific branch's record
     */
    public static function canAccessBranch(?int $branchId): bool
    {
        $admin = self::getCurrentAdmin();
        if (!$admin || empty($admin->branch_id)) {
            return false;
        }

        if (self::isHeadOfficeUser()) {
            return true;
        }

        return (int)$admin->branch_id === (int)$branchId;
    }

    /**
     * Authorize access to a branch or abort with 403
     */
    public static function authorizeBranch(?int $branchId): void
    {
        if (!self::canAccessBranch($branchId)) {
            abort(403, 'Unauthorized access: You do not have permission to view or manage records for this branch.');
        }
    }

    /**
     * Apply strict branch scope to any query builder
     */
    public static function applyScope($query, string $table = '')
    {
        $branchId = self::getActiveBranchId();

        if ($branchId === -1) {
            // User has no branch assigned => force 0 results
            $column = $table ? "{$table}.id" : 'id';
            $query->whereRaw('1 = 0');
            return $query;
        }

        if ($branchId !== null) {
            $column = $table ? "{$table}.branch_id" : 'branch_id';
            $query->where($column, $branchId);
        }

        return $query;
    }

    /**
     * Get the branch_id to propagate to new records
     */
    public static function getBranchIdForNewRecord(?int $requestedBranchId = null): ?int
    {
        $admin = self::getCurrentAdmin();
        if (!$admin) {
            return null;
        }

        if (self::isHeadOfficeUser()) {
            if ($requestedBranchId) {
                return (int) $requestedBranchId;
            }
            $active = self::getActiveBranchId();
            if ($active && $active !== -1) {
                return $active;
            }
            return (int) $admin->branch_id;
        }

        return (int) $admin->branch_id;
    }
}
