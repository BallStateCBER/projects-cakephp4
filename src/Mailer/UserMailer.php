<?php
declare(strict_types=1);

namespace App\Mailer;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Mailer\Message;
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
        $siteTitle = Configure::read('DataCenter.siteTitle');
        $this
            ->setTo($user->email)
            ->setSubject("$siteTitle - Reset password")
            ->setEmailFormat(Message::MESSAGE_BOTH)
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
