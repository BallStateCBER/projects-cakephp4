<?php
/**
 * @var \App\Model\Entity\Tag[]|\Cake\ORM\ResultSet $tags
 * @var \App\View\AppView $this
 */
?>

<p>
    <?= $this->Html->link(
        'Add a New Tag',
        ['action' => 'add'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<table class="partners table">
    <?php foreach ($tags as $tag): ?>
        <tr>
            <td>
                <?= $tag->uc_name ?>
            </td>
            <td class="actions">
                <?= $this->Html->link(
                    'Edit',
                    [
                        'action' => 'edit',
                        $tag->id,
                    ]
                ) ?>
                <?php
                    $prompt = 'Are you sure you want to delete this?';
                    if ($tag->releases) {
                        $count = count($tag->releases);
                        $prompt .= sprintf(
                            ' %s %s will be affected.',
                            $count,
                            __n('release', 'releases', $count)
                        );
                    }
                    echo $this->Form->postLink(
                        'Delete',
                        [
                            'action' => 'delete',
                            $tag->id,
                        ],
                        ['confirm' => $prompt]
                    );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
