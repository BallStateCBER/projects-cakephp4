<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AuthorsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AuthorsTable Test Case
 */
class AuthorsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AuthorsTable
     */
    protected $Authors;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Authors',
        'app.Releases',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Authors') ? [] : ['className' => AuthorsTable::class];
        $this->Authors = $this->getTableLocator()->get('Authors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Authors);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
