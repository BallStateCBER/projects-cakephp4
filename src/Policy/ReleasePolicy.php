<?php
declare(strict_types=1);

namespace App\Policy;

use Authorization\Policy\BeforePolicyInterface;

class ReleasePolicy implements BeforePolicyInterface
{
    /**
     * A policy that allows any logged-in user to access any release
     *
     * @param \Authorization\IdentityInterface|null $user User
     * @param mixed $resource Resource
     * @param string $action Action
     * @return bool
     */
    public function before($user, $resource, $action)
    {
        return true;
    }
}
