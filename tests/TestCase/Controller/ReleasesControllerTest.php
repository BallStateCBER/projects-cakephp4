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
        $release = $releasesTable->get(ReleasesFixture::RELEASE_WITH_GRAPHICS, ['contain' => ['Graphics']]);
        $url = [
            'controller' => 'Releases',
            'action' => 'view',
            'id' => $release->id,
            'slug' => $release->slug,
        ];
        $this->get($url);
        $this->assertResponseOk();
        $this->assertResponseContains($release->title);
        $this->assertResponseContains('<meta property="og:type" content="article"');
        foreach ($release->graphics as $graphic) {
            $imgPath = Router::url("/img/releases/$graphic->dir/$graphic->image", true);
            $this->assertResponseContains(sprintf('<meta property="og:image" content="%s"', $imgPath));
            $this->assertResponseContains(sprintf('src="%s"', $graphic->thumbnailFullPath));
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
