<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Release Entity
 *
 * @property int $id
 * @property string $description
 * @property string $slug
 * @property string $title
 * @property string $url
 * @property \Cake\I18n\FrozenDate $released
 * @property int|null $partner_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Partner|null $partner
 * @property \App\Model\Entity\Graphic[] $graphics
 * @property \App\Model\Entity\Author[] $authors
 * @property \App\Model\Entity\Tag[] $tags
 */
class Release extends Entity
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
        'title' => true,
        'slug' => true,
        'description' => true,
        'released' => true,
        'partner_id' => true,
        'created' => true,
        'modified' => true,
        'partner' => true,
        'graphics' => true,
        'authors' => true,
        'tags' => true,
    ];

    /**
     * Returns this release's /releases/view URL
     *
     * @return string
     */
    protected function _getUrl(): string
    {
        return Router::url([
            'controller' => 'Releases',
            'action' => 'view',
            'id' => $this->id,
            'slug' => $this->slug,
        ]);
    }
}
