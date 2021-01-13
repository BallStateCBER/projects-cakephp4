<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\IdentityInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\ServerRequest;

/**
 * Request policy
 */
class RequestPolicy
{
    /**
     * Method to check if the request can be accessed
     *
     * @param \Authorization\IdentityInterface|null $identity Identity
     * @param \Cake\Http\ServerRequest $request Server Request
     * @return bool
     */
    public function canAccess(?IdentityInterface $identity, ServerRequest $request)
    {
        // All authenticated users can access all actions
        if ($identity) {
            return true;
        }

        // All DataCenter and DebugKit actions are accessible to unauthenticated users
        $plugin = $request->getParam('plugin');
        if (in_array($plugin, ['DataCenter', 'DebugKit'])) {
            return true;
        }

        // Fetch actions that are accessible to unauthenticated users
        $controller = $request->getParam('controller');
        $controllerClass = 'App\Controller\\' . $controller . 'Controller';
        if (!defined("$controllerClass::ALLOW")) {
            throw new InternalErrorException($controllerClass . ' allow list not found');
        }
        $action = $request->getParam('action');

        return in_array($action, $controllerClass::ALLOW);
    }
}
