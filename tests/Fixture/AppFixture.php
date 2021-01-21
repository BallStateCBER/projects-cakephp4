<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ReleasesFixture
 */
class AppFixture extends TestFixture
{
    protected array $defaultRecord = [];

    /**
     * Convenience method for merging data with default data and adding to $this->records
     *
     * @param array $data Record data
     * @return void
     */
    protected function addRecord(array $data): void
    {
        $this->records[] = array_merge($this->defaultRecord, $data);
    }
}
