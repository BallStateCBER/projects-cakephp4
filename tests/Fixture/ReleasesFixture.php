<?php
declare(strict_types=1);

namespace App\Test\Fixture;

/**
 * ReleasesFixture
 */
class ReleasesFixture extends AppFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'title' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'slug' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'released' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'partner_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => ''],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => ''],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // phpcs:enable

    protected array $defaultRecord = [
        'title' => 'Release Title',
        'slug' => 'release-title',
        'description' => 'Release description.',
        'released' => '2021-01-01',
        'partner_id' => 1,
        'created' => '2021-01-01 01:01:00',
        'modified' => '2021-01-01 01:01:00',
    ];

    public const RELEASE_WITH_GRAPHICS = 1;

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->addRecord([
            'id' => self::RELEASE_WITH_GRAPHICS,
        ]);
        parent::init();
    }
}
