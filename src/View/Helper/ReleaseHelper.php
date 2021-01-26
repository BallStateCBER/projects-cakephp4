<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\View\Helper;

/**
 * Release helper
 */
class ReleaseHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Returns a string that will display any of this entity's errors
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @return string|null
     */
    public function displayErrors(EntityInterface $entity)
    {
        if (!$entity->hasErrors()) {
            return null;
        }
        $errors = $entity->getErrors();
        $errorMsgsPerField = array_map(function ($errorMsgs, $field) {
            return $field . ': ' . implode('; ', $errorMsgs);
        }, $errors, array_keys($errors));

        return '<p class="alert alert-danger">' . implode('<br />', $errorMsgsPerField) . '</p>';
    }
}
