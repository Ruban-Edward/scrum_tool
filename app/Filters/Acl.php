<?php

/**
 * Acl.php
 * 
 * @category Filter
 * @author   Ruban Edward
 * 
 * @purpose  Filter used in routes to allow the user to access pages based on Role
 */

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\Admin\PermissionModel;
use App\Models\Admin\RolePermissionModel;

class Acl implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userRoleId = $session->get('role_id');

        // Get the current URL
        $currentURL = $request->getUri()->getRoutePath();

        // Load models
        $permissionModel = model(PermissionModel::class);
        $rolePermissionModel = model(RolePermissionModel::class);

        if ($userRoleId != '1') {
            // Get the required permission for the current URL
            $permission = $permissionModel->where('routes_url', $currentURL)->first();

            if ($permission) {
                $permissionId = $permission['permission_id'];

                // Check if the user's role has this permission
                $rolePermission = $rolePermissionModel->where('r_role_id', $userRoleId)
                    ->where('r_permission_id', $permissionId)
                    ->where('is_deleted', 'N')
                    ->first();

                if (!$rolePermission) {
                    return redirect()->to('/no_access');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // while not happens after request
    }
}
