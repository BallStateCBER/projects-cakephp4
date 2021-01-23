<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Fixture\ReleasesFixture;
use App\Test\Fixture\UsersFixture;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

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
    }

    /**
     * Simulates the session of a logged-in user
     *
     * @return void
     */
    private function setUserSession(): void
    {
        $usersFixture = new UsersFixture();
        $userData = $usersFixture->records[0];
        $this->session([
            'Auth' => ['User' => $userData],
            //'Auth' => ['User' => $this->getTableLocator()->get('Users')->find()->first()],
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
            ->orderDesc('id')
            ->first();
        $this->assertRedirect($newRelease->url);
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
