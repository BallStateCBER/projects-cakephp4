<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tag Entity
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $parent_id
 * @property int|null $lft
 * @property int|null $rght
 * @property bool $selectable
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ParentTag $parent_tag
 * @property \App\Model\Entity\ChildTag[] $child_tags
 * @property \App\Model\Entity\Release[] $releases
 */
class Tag extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'slug' => true,
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'selectable' => true,
        'created' => true,
        'modified' => true,
        'parent_tag' => true,
        'child_tags' => true,
        'releases' => true,
    ];
}
