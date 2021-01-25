<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Model\Table\GraphicsTable;
use App\Test\Fixture\ReleasesFixture;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
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
        'tags' => ['1', '2'],
        'custom_tags' => 'New Tag 3, New Tag 4',

    ];

    private string $addUrl = '/releases/add';

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
                'title' => 'Mask',
                'url' => 'https://google.com',
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
                'title' => 'Crook',
                'url' => 'https://theEther.com',
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
            ->contain(['Graphics'])
            ->orderDesc('id')
            ->first();
        $this->assertRedirect($newRelease->url);

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
     * Test edit method
     *
     * @return void
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
