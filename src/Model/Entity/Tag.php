<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * Tag Entity
 *
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property bool $selectable
 * @property int $id
 * @property int $parent_id
 * @property int|null $lft
 * @property int|null $rght
 * @property string $name
 * @property string $slug
 * @property string $uc_name
 *
 * @property \App\Model\Entity\Tag $parent_tag
 * @property \App\Model\Entity\Tag[] $child_tags
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

    /**
     * Virtual field for capitalized name
     *
     * @return string
     */
    protected function _getUcName(): string
    {
        $ucName = ucwords($this->name);
        $lcWords = ['and'];
        foreach ($lcWords as $lcWord) {
            $ucName = str_ireplace(" $lcWord ", " $lcWord ", $ucName);
        }

        return $ucName;
    }
}
