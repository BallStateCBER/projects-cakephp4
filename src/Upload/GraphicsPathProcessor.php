<?php
namespace App\Upload;

use Cake\ORM\TableRegistry;
use Josegonzalez\Upload\File\Path\DefaultProcessor;

class GraphicsPathProcessor extends DefaultProcessor
{
    /**
     * Returns the basepath for the current field/data combination.
     * If a `path` is specified in settings, then that will be used as
     * the replacement pattern
     *
     * @return string
     * @throws \LogicException if a replacement is not valid for the current dataset
     */
    public function basepath(): string
    {
        /** @var \App\Model\Table\GraphicsTable $graphicsTable */
        $graphicsTable = TableRegistry::getTableLocator()->get('Graphics');

        return $graphicsTable->getNextDirNumber();
    }
}
