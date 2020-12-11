<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Cache\Cache;
use Cake\ORM\Query;

/**
 * Tags Controller
 *
 * @property \App\Model\Table\TagsTable $Tags
 * @method \App\Model\Entity\Tag[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TagsController extends AppController
{
    public const ALLOW = ['index', 'view'];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->set([
            'pageTitle' => 'Tags',
            'tags' => $this->Tags
                ->find()
                ->contain(['Releases'])
                ->orderAsc('name')
                ->all(),
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tag = $this->Tags->get($id, [
            'contain' => [
                'Releases' => function (Query $q) {
                    return $q->orderDesc('released');
                },
            ],
        ]);

        $this->set([
            'tag' => $tag,
            'pageTitle' => str_replace(' And ', ' and ', ucwords($tag->name)),
        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tag = $this->Tags->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['selectable'] = true;
            $tag = $this->Tags->patchEntity($tag, $data);
            if ($this->Tags->save($tag)) {
                $this->Flash->success('The tag has been saved.');
                Cache::delete('sidebar_tags', 'long');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The tag could not be saved. Please, try again.');
        }
        $pageTitle = 'Add a New Tag';
        $this->set(compact('tag', 'pageTitle'));

        return $this->render('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tag = $this->Tags->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tag = $this->Tags->patchEntity($tag, $this->request->getData());
            if ($this->Tags->save($tag)) {
                $this->Flash->success('The tag has been saved.');
                Cache::delete('sidebar_tags', 'long');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error('The tag could not be saved. Please try again.');
        }
        $pageTitle = 'Edit Tag';
        $this->set(compact('tag', 'pageTitle'));

        return $this->render('form');
    }

    /**
     * Delete method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tag = $this->Tags->get($id);
        if ($this->Tags->delete($tag)) {
            $this->Flash->success(__('The tag has been deleted.'));
            Cache::delete('sidebar_tags', 'long');
        } else {
            $this->Flash->error(__('The tag could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
