<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GraphicsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GraphicsTable Test Case
 */
class GraphicsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\GraphicsTable
     */
    protected $Graphics;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Graphics',
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
        $config = $this->getTableLocator()->exists('Graphics') ? [] : ['className' => GraphicsTable::class];
        $this->Graphics = $this->getTableLocator()->get('Graphics', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Graphics);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
