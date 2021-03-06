<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Release;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\Routing\Router;
use SplFileInfo;

/**
 * Releases Controller
 *
 * @method \App\Model\Entity\Release[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 * @property \App\Model\Table\AuthorsTable $Authors
 * @property \App\Model\Table\GraphicsTable $Graphics
 * @property \App\Model\Table\PartnersTable $Partners
 * @property \App\Model\Table\ReleasesTable $Releases
 * @property \App\Model\Table\TagsTable $Tags
 * @property \DataCenter\Controller\Component\TagManagerComponent $TagManager
 */
class ReleasesController extends AppController
{
    public const ALLOW = [
        'index',
        'latest',
        'listReports',
        'reportNotFound',
        'search',
        'updateDataCenterHome',
        'view',
        'year',
    ];
    private array $reportFiletypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event An Event instance
     * @return \Cake\Http\Response|void|null
     * @throws \Exception
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('DataCenter.TagManager');
    }

    /**
     * Home page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Releases
            ->find()
            ->contain(['Partners', 'Graphics', 'Authors', 'Tags'])
            ->orderDesc('Releases.id');

        $releases = $this->paginate($query);
        $pageTitle = false;

        $this->set(compact('releases', 'pageTitle'));
    }

    /**
     * View Release page
     *
     * @param string|null $id Release id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $release = $this->Releases->get($id, [
            'contain' => ['Partners', 'Graphics', 'Authors', 'Tags'],
        ]);
        $pageTitle = $release->title;
        $ogMetaTags = [
            'og:description' => $release->description,
            'og:type' => 'article',
            'og:url' => Router::url($release->url, true),
        ];
        foreach ($release->graphics as $graphic) {
            $ogMetaTags['og:image'][] = sprintf(
                Router::url('/img/releases/%s/%s', true),
                $graphic->dir,
                $graphic->image
            );
        }
        $linkPageTitle = true;

        $this->set(compact(
            'linkPageTitle',
            'ogMetaTags',
            'pageTitle',
            'release',
        ));
    }

    /**
     * Add a New Release page
     *
     * @return \Cake\Http\Response Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->loadModel('Partners');
        $this->loadModel('Authors');
        $release = $this->Releases->newEmptyEntity();

        if ($this->request->is('post')) {
            $release = $this->processForm($release);

            if ($this->Releases->save($release)) {
                $this->Flash->success('Release added');
                Cache::delete('sidebar_tags', 'long');
                Cache::delete('sidebar_partners', 'long');

                return $this->redirect($release->url);
            } else {
                $this->Flash->error(
                    'The release could not be saved. Please correct any indicated errors and try again.'
                );
            }
        }

        $this->set(['pageTitle' => 'Add a New Release']);
        $this->setReleaseFormVars($release);

        return $this->render('/Releases/form');
    }

    /**
     * Edit Release page
     *
     * @param string|null $releaseId Release id.
     * @return \Cake\Http\Response Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($releaseId = null)
    {
        $release = $this->Releases->get($releaseId, [
            'contain' => ['Authors', 'Graphics', 'Partners', 'Tags'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $release = $this->processForm($release);

            if ($this->Releases->save($release)) {
                $this->Flash->success('Release updated');
                Cache::delete('sidebar_tags', 'long');
                Cache::delete('sidebar_partners', 'long');
                $this->redirect($release->url);
            } else {
                $this->Flash->error(
                    'The release could not be updated. Please correct any indicated errors and try again.'
                );
            }
        }

        $this->set(['pageTitle' => 'Edit ' . $release->title]);
        $this->setReleaseFormVars($release);

        return $this->render('/Releases/form');
    }

    /**
     * Delete method
     *
     * @param string|null $releaseId Release id
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($releaseId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $release = $this->Releases->get($releaseId);
        if ($this->Releases->delete($release)) {
            $this->Flash->success('The release has been deleted.');
        } else {
            $this->Flash->error('There was an error deleting this release');
        }

        return $this->redirect($this->request->referer());
    }

    /**
     * Reads request data, finds or creates a Partner entity, adds it to the current release, and returns the release
     *
     * @param \App\Model\Entity\Release $release Release entity
     * @return \App\Model\Entity\Release
     */
    private function processNewPartner(Release $release)
    {
        $newPartnerName = $this->request->getData('new_partner');
        if (!$newPartnerName) {
            return $release;
        }

        $newPartnerName = trim($newPartnerName);
        $partnerData = ['name' => $newPartnerName];
        $partnerExists = $this->Partners->exists($partnerData);
        if ($partnerExists) {
            $partner = $this->Partners
                ->find()
                ->where($partnerData)
                ->first();
            $release->partner = $partner;
        } else {
            $partnerData['short_name'] = $newPartnerName;
            $partner = $this->Partners->newEntity($partnerData);
            if ($this->Partners->save($partner)) {
                $release->partner = $partner;
            } else {
                $this->Flash->error("There was an error saving the partner $newPartnerName");
            }
        }

        return $release;
    }

    /**
     * Reads request data, creates Author entities, adds them to the current release, and returns it
     *
     * @param \App\Model\Entity\Release $release Release entity
     * @return \App\Model\Entity\Release
     */
    private function processNewAuthors(Release $release)
    {
        $this->loadModel('Authors');
        $newAuthors = $this->request->getData('new_authors') ?? [];
        foreach ($newAuthors as $authorName) {
            $author = $this->Authors->newEntity(['name' => $authorName]);
            $this->Authors->save($author);
            $release->authors[] = $author;
        }

        return $release;
    }

    /**
     * Sets the $availableTags variable in the view
     *
     * @return void
     */
    private function setAvailableTags()
    {
        $this->loadModel('Tags');
        $this->set([
            'availableTags' => $this->Tags
                ->find('threaded')
                ->select(['id', 'name', 'parent_id', 'selectable'])
                ->orderAsc('name'),
        ]);
    }

    /**
     * Updates the $release entity using request data and returns the updated entity
     *
     * @param \App\Model\Entity\Release $release Release entity
     * @return \App\Model\Entity\Release
     */
    private function processForm(Release $release)
    {
        $this->loadModel('Graphics');
        $nextGraphicsDir = $this->Graphics->getNextDirNumber();
        $data = $this->request->getData();
        foreach ($data['graphics'] ?? [] as $k => $graphic) {
            if ($graphic['id'] ?? false) {
                if ($graphic['remove']) {
                    $this->Releases->Graphics->delete($this->Releases->Graphics->get($graphic['id']));
                    unset($data['graphics'][$k]);
                }
            } else {
                $data['graphics'][$k]['dir'] = $nextGraphicsDir;
            }
        }

        $release = $this->Releases->patchEntity($release, $data);

        $this->loadModel('Tags');
        $release->tags = $this->TagManager->processTagInput($this->request->getData(), $this->Tags);
        $release = $this->processNewAuthors($release);
        $release = $this->processNewPartner($release);

        return $release;
    }

    /**
     * Lists all releases released in the specified year
     *
     * @param null|string $year Publishing year
     * @return void
     */
    public function year($year = null)
    {
        $releases = $this->Releases
            ->find()
            ->select(['id', 'title', 'slug', 'released'])
            ->where(function (QueryExpression $exp) use ($year) {
                return $exp->like('released', "$year%");
            })
            ->orderAsc('released');

        $this->set([
            'ogMetaTags' => [
                'og:description' => "Projects and publications published in $year",
            ],
            'pageTitle' => "$year Projects and Publications",
            'releases' => $releases,
            'year' => $year,
        ]);
    }

    /**
     * Search results page
     *
     * @return void
     */
    public function search()
    {
        $searchTerm = $this->request->getQuery('term');
        $searchTerm = $searchTerm ? trim($searchTerm) : $searchTerm;

        if ($searchTerm) {
            $query = $this->Releases
                ->find()
                ->select(['id', 'title', 'slug', 'released', 'description'])
                ->where([
                    'OR' => [
                        function (QueryExpression $exp) use ($searchTerm) {
                            return $exp->like('title', "%$searchTerm%");
                        },
                        function (QueryExpression $exp) use ($searchTerm) {
                            return $exp->like('description', "%$searchTerm%");
                        },
                    ],
                ])
                ->orderDesc('released');
            $releases = $this->paginate($query);

            $tags = $this->Tags
                ->find()
                ->select(['id', 'name', 'slug'])
                ->where(function (QueryExpression $exp) use ($searchTerm) {
                    return $exp->like('name', "%$searchTerm%");
                })
                ->all();
        } else {
            $releases = [];
            $tags = [];
        }

        $this->set([
            'pageTitle' => 'Search Results' . ($searchTerm ? ": $searchTerm" : ''),
            'releases' => $releases,
            'tags' => $tags,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Sets view variables for the release form page
     *
     * @param \App\Model\Entity\Release $release Release entity
     * @return void
     */
    private function setReleaseFormVars(Release $release)
    {
        // Determine file upload limit
        $maxUpload = (int)(ini_get('upload_max_filesize'));
        $maxPost = (int)(ini_get('post_max_size'));
        $memoryLimit = (int)(ini_get('memory_limit'));
        $uploadMb = min($maxUpload, $maxPost, $memoryLimit);

        $time = time();
        $token = md5(Configure::read('upload_token') . $time);
        $action = $this->request->getParam('action');

        $alternateTemplates = [
            'inputContainer' => '<div class="form-group form-row {{type}}{{required}}">' .
                '<div class="col-8">{{content}}</div><div class="col-4 d-flex align-items-end">{{after}}</div></div>',
            'select' => '<select class="form-control" name="{{name}}"{{attrs}}>{{content}}</select>',
        ];
        $alternateTemplates['inputContainerError'] = str_replace(
            'form-group',
            'form-group is-invalid',
            $alternateTemplates['inputContainer']
        );
        $alternateTemplates['inputContainerError'] = str_replace(
            '{{content}}',
            '{{content}}{{error}}',
            $alternateTemplates['inputContainerError']
        );
        $defaultTemplates = include str_replace(
            '\\',
            DS,
            ROOT . '\vendor\ballstatecber\datacenter-plugin-cakephp4\config\bootstrap_form.php'
        );

        $buttonAppendTemplate = [
            'inputContainer' => '<div class="input-group {{type}}{{required}}">{{content}}' .
                '<div class="input-group-append">{{after}}</div></div>',
        ];

        $this->loadModel('Authors');
        $this->loadModel('Partners');
        $authors = $this->Authors->find()->orderAsc('name')->all();
        $partners = $this->Partners->find()->orderAsc('name')->all();

        $reportFiletypes = $this->reportFiletypes;

        $this->set(compact(
            'action',
            'alternateTemplates',
            'authors',
            'buttonAppendTemplate',
            'defaultTemplates',
            'partners',
            'release',
            'reportFiletypes',
            'time',
            'token',
            'uploadMb',
        ));

        $this->setAvailableTags();
    }

    /**
     * Displays a page that lists all report documents in /webroot/reports
     *
     * @return void
     */
    public function listReports()
    {
        $filenames = scandir(WWW_ROOT . 'reports');
        $filesNewest = [];
        $filesAlphabetic = [];
        foreach ($filenames as $i => $filename) {
            if (in_array($filename, ['.', '..'])) {
                continue;
            }
            $file = (new SplFileInfo(WWW_ROOT . 'reports' . DS . $filename))->getFileInfo();
            $lastChange = $file->getMTime();
            $fileInfo = [
                'filename' => $filename,
                'timestamp' => $lastChange,
                'date' => date('r', $lastChange),
            ];
            $filesNewest["$lastChange.$i"] = $fileInfo;
            $filesAlphabetic[$filename] = $fileInfo;
        }
        krsort($filesNewest);
        ksort($filesAlphabetic);
        $this->set(compact('filesNewest', 'filesAlphabetic'));
        $this->viewBuilder()->setLayout('ajax');
    }

    /**
     * The endpoint for uploading reports
     *
     * @return void
     */
    public function uploadReport()
    {
        $this->viewBuilder()->setLayout('ajax');

        // Confirm a file upload
        if (empty($_POST) || empty($_FILES)) {
            $msg = 'Error: File was not successfully uploaded. This may be because the file exceeded a size limit.';
            $this->set('msg', $msg);
            $this->response = $this->response->withStatus(400, $msg);

            return;
        }

        // Validate the token
        $timestamp = $this->request->getData('timestamp');
        $requestToken = $this->request->getData('token');
        $validToken = md5(Configure::read('upload_token') . $timestamp);
        if ($requestToken != $validToken) {
            $msg = 'Error: Security token incorrect.';
            $this->set('msg', $msg);
            $this->response = $this->response->withStatus(400, $msg);

            return;
        }

        // Validate the file type
        $filename = $_FILES['report']['name'];
        $fileParts = pathinfo($filename);
        $validExtensions = $this->reportFiletypes;
        $isValid = in_array(strtolower($fileParts['extension']), $validExtensions);
        if (!$isValid) {
            $msg = sprintf(
                'Error: %s does not have one of these allowed extensions: %s',
                $filename,
                implode(', ', $validExtensions)
            );
            $this->set('msg', $msg);
            $this->response = $this->response->withStatus(400, $msg);

            return;
        }

        $targetFolder = 'reports'; // Relative to the root
        $targetPath = WWW_ROOT . $targetFolder;
        $targetFile = rtrim($targetPath, '/') . '/' . $filename;
        $overwrite = (bool)$this->request->getData('overwrite');
        if (file_exists($targetFile) && !$overwrite) {
            $msg = "Error: $filename has already been uploaded.";
            $this->response = $this->response->withStatus(400, $msg);
        } elseif (move_uploaded_file($_FILES['report']['tmp_name'], $targetFile)) {
            $msg = "$filename uploaded";
        } else {
            $msg = "Error uploading $filename";
            $this->response = $this->response->withStatus(500, $msg);
        }

        $this->set('msg', $msg);
    }

    /**
     * Index page for admins
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function admin()
    {
        $query = $this->Releases
            ->find()
            ->orderDesc('Releases.id');

        $releases = $this->paginate($query);
        $pageTitle = 'Releases';

        $this->set(compact('pageTitle', 'releases'));
    }

    /**
     * Provides a printout of JSON-encoded data used by the Data Center's homepage
     *
     * @return void
     */
    public function latest()
    {
        /** @var \App\Model\Entity\Release|array $release */
        $release = $this->Releases
            ->find()
            ->select(['id', 'title', 'released', 'slug'])
            ->contain([
                'Graphics' => function (Query $q) {
                    return $q
                        ->select(['id', 'release_id', 'dir', 'image'])
                        ->orderAsc('weight')
                        ->limit(1);
                },
            ])
            ->orderDesc('released')
            ->first();

        if ($release) {
            $release->url = Router::url([
                'controller' => 'Releases',
                'action' => 'view',
                'id' => $release->id,
                'slug' => $release->slug,
            ], true);
            if ($release->graphics) {
                $release->graphic = Router::url($release->graphics[0]->thumbnailFullPath, true);
                unset($release->graphics);
            }
        }
        $this->set([
            'release' => $release,
            '_serialize' => ['release'],
        ]);
    }

    /**
     * Requests for reports that don't exist have this page returned
     *
     * @return void
     */
    public function reportNotFound(): void
    {
        $this->set(['pageTitle' => 'File not found']);
    }
}
