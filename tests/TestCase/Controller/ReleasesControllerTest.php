<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Model\Table\GraphicsTable;
use App\Test\Fixture\ReleasesFixture;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use FilesystemIterator;
use Laminas\Diactoros\UploadedFile;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use const UPLOAD_ERR_OK;

/**
 * App\Controller\ReleasesController Test Case
 *
 * @uses \App\Controller\ReleasesController
 * @property \App\Model\Table\ReleasesTable $Releases
 */
class ReleasesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Authors',
        'app.AuthorsReleases',
        'app.Graphics',
        'app.Partners',
        'app.Releases',
        'app.ReleasesTags',
        'app.Tags',
        'app.Users',
    ];

    private array $releasePostData = [
        'title' => 'Release Title',
        'released' => '2021-01-01',
        'partner_id' => '1',
        'new_partner' => '',
        'author_select' => '',
        'new_author_input' => '',
        'authors' => ['_ids' => ['1']],
        'new_authors' => ['New Author 1', 'New Author 2'],
        'description' => '<p>Release description</p>',
        'tags' => ['_ids' => ['1', '2']],
        'custom_tags' => 'New Tag 3, New Tag 4',
    ];

    private string $addUrl = '/releases/add';
    private string $editUrl = '/releases/edit/';

    /**
     * Sets up the test case
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        $this->Releases = $this->getTableLocator()->get('Releases');
        $this->releasePostData['graphics'] = [
            [
                'title' => 'Image 1',
                'url' => 'https://example.com',
                'weight' => '0',
                'image' => new UploadedFile(
                    TESTS . 'FilesToUpload' . DS . 'image.tmp',
                    29999,
                    UPLOAD_ERR_OK,
                    'image.png',
                    'image/png'
                ),
            ],
            [
                'title' => 'Image 2',
                'url' => 'https://example.com',
                'weight' => '1',
                'image' => new UploadedFile(
                    TESTS . 'FilesToUpload' . DS . 'image2.tmp',
                    29999,
                    UPLOAD_ERR_OK,
                    'image2.png',
                    'image/png'
                ),
            ],
        ];
    }

    /**
     * Cleans up uploaded files after a test has concluded
     */
    public function tearDown(): void
    {
        parent::tearDown();

        // Delete all uploaded images
        $dir = new RecursiveDirectoryIterator(GraphicsTable::GRAPHICS_DIR_ROOT_TESTING, FilesystemIterator::SKIP_DOTS);
        $dir = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($dir as $file) {
            if ($file->getFilename() == '.gitkeep') {
                continue;
            }
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }
    }

    /**
     * Simulates the session of a logged-in user
     *
     * @return void
     */
    private function setUserSession(): void
    {
        $this->session([
            'Auth' => $this->getTableLocator()->get('Users')->find()->first(),
        ]);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->get([
            'controller' => 'Releases',
            'action' => 'index',
        ]);
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView(): void
    {
        $release = $this->Releases->get(
            ReleasesFixture::RELEASE_WITH_GRAPHICS,
            ['contain' => ['Authors', 'Graphics', 'Partners']]
        );

        $this->get($release->url);
        $this->assertResponseOk();
        $this->assertResponseContains($release->title);
        $this->assertResponseContains('<meta property="og:type" content="article"');

        // Assert partner link inside of p.partner
        $this->assertResponseRegExp(sprintf(
            '/%s%s%s%s%s/',
            preg_quote('<p class="partner">'),
            '\\s*',
            preg_quote(sprintf(
                '<a href="%s">%s</a>',
                $release->partner->url,
                $release->partner->name
            ), '/'),
            '\\s*',
            preg_quote('</p>', '/')
        ));

        // Assert all author links inside of p.authors
        foreach ($release->authors as $author) {
            $this->assertResponseRegExp(sprintf(
                '/%s%s%s%s%s/',
                preg_quote('<p class="authors">'),
                '[\s\S]*',
                preg_quote(sprintf(
                    '<a href="%s">%s</a>',
                    $author->url,
                    $author->name
                ), '/'),
                '[\s\S]*',
                preg_quote('</p>', '/')
            ));
        }

        // Assert that every image has a matching meta tag and image tag
        foreach ($release->graphics as $graphic) {
            $imgPath = Router::url("/img/releases/$graphic->dir/$graphic->image", true);
            $this->assertResponseContains(sprintf('<meta property="og:image" content="%s"', $imgPath));
            $this->assertResponseContains(sprintf('<img src="%s"', $graphic->thumbnailFullPath));
        }
    }

    /**
     * Tests that valid data can be POSTed successfully
     *
     * @return void
     */
    public function testAddSuccess(): void
    {
        $this->setUserSession();
        $this->post($this->addUrl, $this->releasePostData);

        /** @var \App\Model\Entity\Release $newRelease */
        $newRelease = $this->Releases
            ->find()
            ->contain(['Authors', 'Graphics', 'Tags'])
            ->orderDesc('id')
            ->first();
        $this->assertRedirect($newRelease->url);

        $this->assertEquals($this->releasePostData['released'], $newRelease->released->format('Y-m-d'));
        foreach ($this->releasePostData['authors']['_ids'] as $authorId) {
            $newReleaseAuthorIds = Hash::extract($newRelease->authors, '{n}.id');
            $this->assertContains($authorId, $newReleaseAuthorIds, 'Expected author ID not found');
        }

        foreach ($this->releasePostData['tags']['_ids'] as $tagId) {
            $savedTagNames = [];
            foreach ($newRelease->tags as $tag) {
                $savedTagNames[] = $tag->id;
            }
            $this->assertContains($tagId, $savedTagNames, 'Expected tag ID not found');
        }
        $fields = [
            'title',
            'partner_id',
            'description',
        ];
        foreach ($fields as $field) {
            $this->assertEquals(
                $this->releasePostData[$field],
                $newRelease->$field,
                "$field field has incorrect value"
            );
        }

        $customTagNames = explode(',', $this->releasePostData['custom_tags']);
        $customTagNames = array_map('strtolower', $customTagNames);
        $customTagNames = array_map('trim', $customTagNames);
        foreach ($customTagNames as $tagName) {
            $savedTagNames = [];
            foreach ($newRelease->tags as $tag) {
                $savedTagNames[] = $tag->name;
            }
            $this->assertContains($tagName, $savedTagNames, 'Expected tag name not found');
        }

        $newAuthorNames = array_map('trim', $this->releasePostData['new_authors']);
        foreach ($newAuthorNames as $authorName) {
            $savedAuthorName = [];
            foreach ($newRelease->authors as $author) {
                $savedAuthorName[] = $author->name;
            }
            $this->assertContains($authorName, $savedAuthorName, 'Expected author name not found');
        }

        $this->assertCount(
            count($this->releasePostData['graphics']),
            $newRelease->graphics,
            'Incorrect number of graphics saved'
        );
        foreach ($newRelease->graphics as $graphic) {
            $path = GraphicsTable::GRAPHICS_DIR_ROOT_TESTING . $graphic->dir . DS;
            $this->assertFileExists($path . $graphic->image);
            $this->assertFileExists($path . $graphic->thumbnail);
        }
    }

    /**
     * Tests that valid data can be POSTed successfully
     *
     * @return void
     */
    public function testAddWithoutCustomTags(): void
    {
        $this->setUserSession();
        $data = $this->releasePostData;
        unset($data['custom_tags']);
        $this->post($this->addUrl, $data);

        /** @var \App\Model\Entity\Release $newRelease */
        $newRelease = $this->Releases
            ->find()
            ->contain(['Authors', 'Graphics', 'Tags'])
            ->orderDesc('id')
            ->first();
        $this->assertRedirect($newRelease->url);
    }

    /**
     * Tests that new partners can be added through the release form
     *
     * @return void
     */
    public function testAddNewPartner()
    {
        $this->setUserSession();
        $this->releasePostData['new_partner'] = 'New Partner Name';
        $this->post($this->addUrl, $this->releasePostData);

        /** @var \App\Model\Entity\Release $newRelease */
        $newRelease = $this->Releases
            ->find()
            ->contain(['Partners'])
            ->orderDesc('Releases.id')
            ->first();

        $this->assertEquals(
            $this->releasePostData['new_partner'],
            $newRelease->partner->name,
            'New partner not added'
        );
    }

    /**
     * Tests that a release cannot be added with a specified blank field
     *
     * @param string $field Field name
     * @return void
     */
    private function testAddFailBlankField(string $field)
    {
        $startingCount = $this->Releases->find()->count();

        $this->setUserSession();
        $data = $this->releasePostData;
        unset($data[$field]);
        $this->post($this->addUrl, $data);
        $this->assertNoRedirect("Redirect not expected with missing '$field' field");
        $this->assertResponseContains('Please correct any indicated errors and try again');

        $endingCount = $this->Releases->find()->count();
        $this->assertEquals($startingCount, $endingCount);
    }

    /**
     * Tests that a release cannot be added with a blank description
     *
     * @return void
     */
    public function testAddFailBlankDescription()
    {
        $this->testAddFailBlankField('description');
    }

    /**
     * Tests that a release cannot be added with a blank title
     *
     * @return void
     */
    public function testAddFailBlankTitle()
    {
        $this->testAddFailBlankField('title');
    }

    /**
     * Tests that a release cannot be added with a blank release date
     *
     * @return void
     */
    public function testAddFailBlankReleased()
    {
        $this->testAddFailBlankField('released');
    }

    /**
     * Test that edit method returns a success response and updates the entity if valid data is provided
     *
     * @return void
     */
    public function testEditSuccess(): void
    {
        $data = array_merge(
            $this->releasePostData,
            [
                'title' => 'Edited',
                'description' => '<p>Edited</p>',
                'authors' => ['_ids' => ['2']],
                'new_authors' => ['New Author'],
                'released' => '2099-01-01',
                'tags' => ['_ids' => ['3']],
                'custom_tags' => 'New Tag',
                'partner_id' => '2',
                'graphics' => [
                    [
                        'id' => 1,
                        'title' => 'Image 1 Edited',
                        'url' => 'https://example-edited.com',
                        'weight' => '5',
                        'remove' => 0,
                    ],
                    [
                        'id' => 2,
                        'remove' => 1,
                    ],
                ],
            ]
        );

        $this->setUserSession();
        $releaseId = ReleasesFixture::RELEASE_WITH_GRAPHICS;
        $this->put($this->editUrl . $releaseId, $data);

        $release = $this->Releases->get(
            $releaseId,
            ['contain' => ['Authors', 'Graphics', 'Tags']]
        );
        $this->assertRedirect($release->url);

        $this->assertEquals($data['title'], $release->title);
        $this->assertEquals($data['description'], $release->description);

        $this->assertCount(2, $release->authors);

        $savedAuthorNames = [];
        $savedAuthorIds = [];
        foreach ($release->authors as $author) {
            $savedAuthorNames[] = $author->name;
            $savedAuthorIds[] = $author->id;
        }
        $this->assertContains($data['new_authors'][0], $savedAuthorNames, 'Expected author name not found');
        $this->assertContains($data['authors']['_ids'][0], $savedAuthorIds, 'Expected author ID not found');

        $this->assertEquals($data['released'], $release->released->format('Y-m-d'));

        $this->assertCount(2, $release->tags);

        $customTagNames = explode(',', $data['custom_tags']);
        $customTagNames = array_map('strtolower', $customTagNames);
        $customTagNames = array_map('trim', $customTagNames);
        $savedTagNames = [];
        $savedTagIds = [];
        foreach ($release->tags as $tag) {
            $savedTagNames[] = $tag->name;
            $savedTagIds[] = $tag->id;
        }
        $this->assertContains($customTagNames[0], $savedTagNames, 'Expected tag name not found');
        $this->assertContains($data['tags']['_ids'][0], $savedTagIds, 'Expected tag ID not found');

        $updatedGraphic = $data['graphics'][0];
        foreach (['title', 'url', 'weight'] as $field) {
            $this->assertEquals($updatedGraphic[$field], $release->graphics[0]->$field, "$field field not updated");
        }

        $deletedGraphic = $data['graphics'][1];
        $graphicsTable = $this->getTableLocator()->get('Graphics');
        $this->assertFalse($graphicsTable->exists(['id' => $deletedGraphic['id']]), 'Graphic was not deleted');

        $this->assertEquals($data['partner_id'], $release->partner_id, 'Expected partner ID not found');
    }

    /**
     * Test that the edit form is pre-populated with all expected contents
     *
     * @return void
     */
    public function testEditFormContents(): void
    {
        $this->setUserSession();
        $releaseId = ReleasesFixture::RELEASE_WITH_GRAPHICS;

        $release = $this->Releases->get(
            $releaseId,
            ['contain' => ['Authors', 'Graphics', 'Partners', 'Tags']]
        );

        $this->get($this->editUrl . $releaseId);
        $this->assertResponseOk();

        $selectedTags = [];
        foreach ($release->tags as $tag) {
            $selectedTags[] = [
                'id' => $tag->id,
                'name' => $tag->name,
            ];
            $this->assertResponseContains('"selectedTags":' . json_encode($selectedTags));
        }
        foreach ($release->authors as $author) {
            $selectedAuthorInput = sprintf(
                '<input type="hidden" name="authors[_ids][]" value="%s"',
                $author->id,
            );
            $this->assertResponseContains($selectedAuthorInput);
        }
        foreach ($release->graphics as $graphic) {
            $this->assertResponseContains($graphic->title);
        }
        $selectedPartnerOption = sprintf(
            '<option value="%s" selected="selected">%s</option>',
            $release->partner->id,
            $release->partner->name,
        );
        $this->assertResponseContains($selectedPartnerOption);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete(): void
    {
        $release = $this->Releases->get(ReleasesFixture::RELEASE_WITH_GRAPHICS);
        $this->Releases->delete($release);
        $this->assertFalse($this->Releases->exists(['id' => ReleasesFixture::RELEASE_WITH_GRAPHICS]));
    }
}
