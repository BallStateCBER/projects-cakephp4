<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 * @property \App\Model\Table\PartnersTable $Partners
 * @property \App\Model\Table\ReleasesTable $Releases
 * @property \App\Model\Table\TagsTable $Tags
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');

        $this->loadModel('Tags');
        $this->loadModel('Releases');
        $this->loadModel('Partners');

        $authenticationConfig = Configure::read('Auth.AuthenticationComponent');
        $this->loadComponent('Authentication.Authentication', $authenticationConfig);
    }

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->setSidebarVariables();
    }

    /**
     * Sets variables used by the sidebar element
     *
     * @return void
     */
    private function setSidebarVariables()
    {
        $tags = $this->Tags
            ->find()
            ->select(['Tags.id', 'Tags.name', 'Tags.slug'])
            ->distinct(['Tags.name', 'Tags.slug'])
            ->matching('Releases')
            ->orderAsc('Tags.name')
            ->all();

        $releases = $this->Releases
            ->find()
            ->select(['id', 'released'])
            ->orderDesc('released')
            ->all();
        $years = [];
        foreach ($releases as $release) {
            $year = $release->released->format('Y');
            if (!in_array($year, $years)) {
                $years[] = $year;
            }
        }

        $partners = $this->Partners
            ->find()
            ->select(['Partners.id', 'Partners.name', 'Partners.short_name', 'Partners.slug'])
            ->distinct(['Partners.id', 'Partners.name', 'Partners.short_name', 'Partners.slug'])
            ->matching('Releases')
            ->orderAsc('Partners.name')
            ->all();

        $identity = $this->Authentication->getIdentity();
        $user = $identity ? $identity->getOriginalData() : null;

        $this->set([
            'sidebarVars' => [
                'partners' => $partners,
                'tags' => $tags,
                'user' => $user,
                'years' => $years,
            ],
        ]);
    }
}
