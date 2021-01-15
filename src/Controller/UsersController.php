<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    public const ALLOW = [
        'index',
        'login',
        'logout',
        'requestResetPassword',
        'resetPassword',
        'view',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->set([
            'pageTitle' => 'Users',
            'users' => $this->Users
                ->find()
                ->orderAsc('name')
                ->all(),
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['SocialAccounts'],
        ]);
        $pageTitle = $user->name;

        $this->set(compact('user', 'pageTitle'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set([
            'pageTitle' => 'Add User',
            'user' => $user,
        ]);

        return $this->render('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $newPassword = $this->request->getData('new_password');
            if ($newPassword) {
                $data['password'] = $newPassword;
            }
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success('The user has been saved.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The user could not be saved. Please, try again.');
        }
        $this->set([
            'pageTitle' => 'Edit ' . $user->name,
            'user' => $user,
        ]);

        return $this->render('form');
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login page
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        $result = $this->Authentication->getResult();

        if ($result->isValid()) {
            $target = $this->Authentication->getLoginRedirect() ?? '/';

            return $this->redirect($target);
        }
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error('Invalid email or password');
        }

        $this->set('pageTitle', 'Log in');

        return null;
    }

    /**
     * Logout redirect
     *
     * @return \Cake\Http\Response
     */
    public function logout()
    {
        $this->Authentication->logout();

        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }

    /**
     * Sends the user an email with a password-resetting link
     *
     * @return void
     */
    public function requestResetPassword()
    {
        $user = $this->Users->newEmptyEntity();
        $this->set([
            'pageTitle' => 'Request Password Reset',
            'user' => $user,
        ]);

        if (!$this->getRequest()->is('post')) {
            return;
        }

        $email = $this->request->getData('email');
        /** @var \App\Model\Entity\User $user */
        $user = $this->Users->findByEmail($email)->first();
        if (!$user) {
            $this->Flash->error('Email address not found');

            return;
        }

        // Update token
        $expiration = new Time('now');
        $day = 86400;
        $user->token_expires = $expiration->addSeconds($day);
        $user->token = bin2hex(Security::randomBytes(16));
        if (!$this->Users->save($user)) {
            $this->Flash->error('An error prevented your password reset token from being generated');

            return;
        }

        $this->getMailer('User')->send('resetPassword', [$user]);
        $this->Flash->success('Please check your email for a password reset message');
    }

    /**
     * Sends the user an email with a password-resetting link
     *
     * @param string $token Password-reset token
     * @return null|\Cake\Http\Response
     */
    public function resetPassword(string $token): ?Response
    {
        $loginUrl = ['controller' => 'Users', 'action' => 'login'];

        /** @var \App\Model\Entity\User $user */
        $user = $this->Users->findByToken($token)->first();
        if (!$user) {
            $this->Flash->error('The provided password reset token is invalid or has already been used.');

            return $this->redirect($loginUrl);
        }

        $expired = $user->token_expires->wasWithinLast('1 day');
        if ($expired) {
            $this->Flash->error(
                'The provided password reset token has expired. ' .
                'Click on the "Reset Password" link to receive a new link to reset your password.'
            );

            return $this->redirect($loginUrl);
        }

        $data = $this->request->getData();
        $data['password'] = $data['new_password'];
        $user = $this->Users->patchEntity($user, $data, [
            'fields' => [
                'new_password',
                'password',
                'password_confirm',
            ],
        ]);

        if ($this->Users->save($user)) {
            $this->Flash->success('Your password has been successfully updated. You may now log in.');

            return $this->redirect($loginUrl);
        }

        $this->set([
            'pageTitle' => 'Reset Password',
            'user' => $user,
        ]);

        return null;
    }
}
