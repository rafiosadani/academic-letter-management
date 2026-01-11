<?php

namespace App\Helpers;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PermissionHelper {
    public static function getGroupedPermissions(): Collection
    {
        $permissions = Permission::orderBy('display_group_name')
            ->orderBy('name')
            ->get();

        return $permissions->groupBy('display_group_name')->map(function($groupPermissions, $groupName) {
            $entities = $groupPermissions->groupBy(function ($permission) {
                // Extract entity from permission name
                // Examples:
                // dashboard.view → dashboard
                // master.user.view → user
                // settings.approval_flow.view → approval_flow
                // letter.my.view → my

                $parts = explode('.', $permission->name);

                // If only 2 parts (e.g., dashboard.view), use first part as entity
                if (count($parts) == 2) {
                    return $parts[0];
                }

                // If 3+ parts, use middle part as entity (index 1)
                // settings.approval_flow.view → approval_flow
                // master.user.view → user
                return $parts[1] ?? $parts[0];
            })->map(function ($entityPermissions, $entityName) {
                // Get display name from first permission in entity
                // This uses the display_name from database which already has proper Indonesian names

                $firstPermission = $entityPermissions->first();

                // Extract display name without action (remove last word like "Lihat", "Tambah", etc)
                $displayName = self::extractEntityDisplayName($firstPermission->display_name);

                // Separate permissions by action
                $actions = [
                    'view' => null,
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                    'approve' => null,
                    'reject' => null,
                    'export' => null,
                ];

                foreach ($entityPermissions as $permission) {
                    $parts = explode('.', $permission->name);
                    $action = end($parts); // Get last part (action)

                    if (array_key_exists($action, $actions)) {
                        $actions[$action] = $permission;
                    }
                }

                return [
                    'entity_name' => $entityName,
                    'display_name' => $displayName,
                    'actions' => $actions,
                    'permissions' => $entityPermissions,
                ];
            });

            return [
                'group_name' => $groupName,
                'entities' => $entities,
            ];
        });
    }

    public static function extractEntityDisplayName(string $permissionDisplayName): string
    {
        // Remove common action prefixes
        $actionPrefixes = [
            'Lihat ',
            'Tambah ',
            'Edit ',
            'Hapus ',
            'Approve ',
            'Reject ',
            'Export ',
        ];

        foreach ($actionPrefixes as $prefix) {
            if (str_starts_with($permissionDisplayName, $prefix)) {
                return substr($permissionDisplayName, strlen($prefix));
            }
        }

        return $permissionDisplayName;
    }

    public static function getAvailableActions(Collection $entities): array
    {
        $availableActions = [];

        foreach ($entities as $entity) {
            foreach ($entity['actions'] as $action => $permission) {
                if ($permission !== null && !in_array($action, $availableActions)) {
                    $availableActions[] = $action;
                }
            }
        }

        return $availableActions;
    }
}
