<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

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
        $release = $this->fixtureManager->loaded()['app.Releases']->records[0];
        $url = [
            'controller' => 'Releases',
            'action' => 'view',
            'id' => $release['id'],
            'slug' => $release['slug'],
        ];
        $this->get($url);
        $this->assertResponseOk();
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
