<?php
namespace App\Mailer;

use App\Model\Entity\User;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;

class UserMailer extends Mailer
{
    /**
     * Defines the "reset password" email
     *
     * @param \App\Model\Entity\User $user User entity
     * @return void
     */
    public function resetPassword(User $user)
    {
        $this
            ->setTo($user->email)
            ->setSubject('CBER Projects and Publications - Reset password')
            ->setViewVars([
                'url' => Router::url([
                    'controller' => 'Users',
                    'action' => 'resetPassword',
                    'token' => $user->token,
                    '_full' => true,
                ]),
            ]);

        $this
            ->viewBuilder()
            ->setTemplate('reset_password');
    }
}
