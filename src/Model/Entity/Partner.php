<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * Partner Entity
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property string $slug
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Release[] $releases
 */
class Partner extends Entity
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
        'short_name' => true,
        'slug' => true,
        'created' => true,
        'modified' => true,
        'releases' => true,
    ];

    /**
     * Automatically set the slug field
     *
     * @param string $shortName Password
     * @return string|null
     */
    protected function _setShortName(string $shortName): ?string
    {
        $this->slug = strtolower(Text::slug($shortName));

        return $shortName;
    }
}
