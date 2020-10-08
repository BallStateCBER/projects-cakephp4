<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Release[] $releases
 */
?>
<table class="releases-list table">
    <?php foreach ($releases as $release): ?>
        <tr>
            <td>
                <?= $release->released->format('F j, Y') ?>
            </td>
            <td>
                <?= $this->Html->link(
                    $release['title'],
                    [
                        'controller' => 'Releases',
                        'action' => 'view',
                        'id' => $release->id,
                        'slug' => $release->slug,
                    ]
                ) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
