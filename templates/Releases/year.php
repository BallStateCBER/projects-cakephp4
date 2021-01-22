<?php
/**
 * @var string $year
 * @var \App\Model\Entity\Release[]|\Cake\Collection\CollectionInterface $releases
 * @var \App\View\AppView $this
 */
?>

<?php if ($releases) : ?>
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
    </table
<?php else: ?>
    <p>
        No projects or publications from that year could be found.
    </p>
<?php endif; ?>
