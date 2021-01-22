<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\Fixture\ReleasesFixture;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ReleasesController Test Case
 *
 * @uses \App\Controller\ReleasesController
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

    /**
     * Sets up the test case
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
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
        $releasesTable = $this->getTableLocator()->get('Releases');
        /** @var \App\Model\Entity\Release $release */
        $release = $releasesTable->get(
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
     * Test add method
     *
     * @return void
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
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
