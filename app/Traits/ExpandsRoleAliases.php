<?php

namespace App\Traits;

trait ExpandsRoleAliases
{
    /**
     * Expand role aliases (e.g., 'seller' includes 'both', 'agent' includes 'admin').
     * Centralized logic for consistent role expansion across all middleware.
     *
     * @param array $roles
     * @return array
     */
    protected function expandRoleAliases(array $roles): array
    {
        $expanded = [];
        
        foreach ($roles as $role) {
            $expanded[] = $role;
            
            // 'both' role can access buyer and seller endpoints
            if ($role === 'buyer' || $role === 'seller') {
                if (!in_array('both', $expanded)) {
                    $expanded[] = 'both';
                }
            }
            
            // 'admin' can access agent endpoints
            if ($role === 'agent') {
                if (!in_array('admin', $expanded)) {
                    $expanded[] = 'admin';
                }
            }
        }
        
        return array_unique($expanded);
    }
}

