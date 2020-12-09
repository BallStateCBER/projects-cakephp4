<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\Query;

/**
 * Authors Controller
 *
 * @property \App\Model\Table\AuthorsTable $Authors
 * @method \App\Model\Entity\Author[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AuthorsController extends AppController
{
    public const ALLOW = ['index', 'view'];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $authors = $this->paginate($this->Authors);

        $this->set(compact('authors'));
    }

    /**
     * View method
     *
     * @param string|null $id Author id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $author = $this->Authors->get($id, [
            'contain' => [
                'Releases' => function (Query $q) {
                    return $q->orderDesc('released');
                },
            ],
        ]);

        $this->set([
            'author' => $author,
            'pageTitle' => $author->name,
        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $author = $this->Authors->newEmptyEntity();
        if ($this->request->is('post')) {
            $author = $this->Authors->patchEntity($author, $this->request->getData());
            if ($this->Authors->save($author)) {
                $this->Flash->success(__('The author has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The author could not be saved. Please, try again.'));
        }
        $releases = $this->Authors->Releases->find('list', ['limit' => 200]);
        $this->set(compact('author', 'releases'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Author id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $author = $this->Authors->get($id, [
            'contain' => ['Releases'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $author = $this->Authors->patchEntity($author, $this->request->getData());
            if ($this->Authors->save($author)) {
                $this->Flash->success(__('The author has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The author could not be saved. Please, try again.'));
        }
        $releases = $this->Authors->Releases->find('list', ['limit' => 200]);
        $this->set(compact('author', 'releases'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Author id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $author = $this->Authors->get($id);
        if ($this->Authors->delete($author)) {
            $this->Flash->success(__('The author has been deleted.'));
        } else {
            $this->Flash->error(__('The author could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
