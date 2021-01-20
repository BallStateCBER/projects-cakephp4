<?php
/**
 * @var string $year
 * @var \App\Model\Entity\Release[] $releases
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
    </table
<?php else: ?>
    <p>
        No projects or publications from that year could be found.
    </p>
<?php endif; ?>
