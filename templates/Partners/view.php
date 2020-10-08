<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Partner $partner
 */
?>
<h1 class="page_title">
    Projects and Publications with <?= $partner->name ?>
</h1>

<?php if ($partner->releases): ?>
    <table class="releases-list table">
        <?php foreach ($partner->releases as $release): ?>
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
    </table>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
