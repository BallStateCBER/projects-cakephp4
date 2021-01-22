<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Author Entity
 *
 * @property int $id
 * @property string $name
 * @property string $url
 *
 * @property \App\Model\Entity\Release[] $releases
 */
class Author extends Entity
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
        'releases' => true,
    ];

    /**
     * Returns this author's /authors/view URL
     *
     * @return string
     */
    protected function _getUrl(): string
    {
        return Router::url([
            'prefix' => false,
            'plugin' => false,
            'controller' => 'Authors',
            'action' => 'view',
            'id' => $this->id,
        ]);
    }
}
