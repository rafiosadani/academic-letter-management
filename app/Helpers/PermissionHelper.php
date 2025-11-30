<?php

namespace App\Helpers;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PermissionHelper {
    public static function getGroupedPermissions(): Collection
    {
        $permissions = Permission::orderBy('id')
            ->orderBy('name')
            ->get();

        return $permissions->groupBy('display_group_name')->map(function($groupPermissions, $groupName) {
            $entities = $groupPermissions->groupBy(function ($permission) {
                // Ambil bagian tengah dari name
                // Contoh: master.user.view => user
                //         surat.masuk.view => masuk
                $parts = explode('.', $permission->name);

                // Jika hanya 2 bagian (misal: dashboard.view), gunakan bagian pertama
                if (count($parts) == 2) {
                    return $parts[0];
                }

                // Jika lebih dari 2, ambil bagian tengah (index 1)
                return $parts[1] ?? $parts[0];
            })->map(function ($entityPermissions, $entityName) {
                // Pisahkan per action
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
                    $action = end($parts); // ambil action terakhir

                    if (array_key_exists($action, $actions)) {
                        $actions[$action] = $permission;
                    }
                }

                // Buat display name untuk entity
                $displayName = ucwords(str_replace('_', ' ', $entityName));

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
