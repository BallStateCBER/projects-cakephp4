<?php
/**
 * @var \App\Model\Entity\Author[]|\Cake\ORM\ResultSet $authors
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */
?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<p>
    <?= $this->Html->link(
        'Add a New Author',
        ['action' => 'add'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<table class="partners table">
    <?php foreach ($authors as $author): ?>
        <tr>
            <td>
                <?= $author->name ?>
            </td>
            <td class="actions">
                <?= $this->Html->link(
                    'Edit',
                    [
                        'action' => 'edit',
                        $author->id,
                    ]
                ) ?>
                <?php
                    $prompt = 'Are you sure you want to delete this?';
                    if ($author->releases) {
                        $count = count($author->releases);
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
                            $author->id,
                        ],
                        ['confirm' => $prompt]
                    );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>