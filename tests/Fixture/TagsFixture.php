<?php
declare(strict_types=1);

namespace App\Test\Fixture;

/**
 * TagsFixture
 */
class TagsFixture extends AppFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'slug' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null],
        'parent_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'lft' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'rght' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'selectable' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // phpcs:enable

    private array $defaultData = [
        'id' => 1,
        'name' => 'Tag name',
        'slug' => 'tag-name',
        'parent_id' => 1,
        'lft' => 1,
        'rght' => 1,
        'selectable' => 1,
        'created' => '2021-01-01 01:01:00',
        'modified' => '2021-01-01 01:01:00',
    ];
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->addRecord([
            'id' => 1,
            'name' => 'Tag 1',
            'slug' => 'tag-1',
        ]);
        $this->addRecord([
            'id' => 2,
            'name' => 'Tag 2',
            'slug' => 'tag-2',
        ]);
        parent::init();
    }
}
