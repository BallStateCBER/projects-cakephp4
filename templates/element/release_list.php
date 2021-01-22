<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Release[]|\Cake\Collection\CollectionInterface $releases
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
                    $release->title,
                    $release->url
                ) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
