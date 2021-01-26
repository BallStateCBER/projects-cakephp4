<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\ReleaseHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\ReleaseHelper Test Case
 */
class ReleaseHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\ReleaseHelper
     */
    protected $Release;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Release = new ReleaseHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Release);

        parent::tearDown();
    }
}
