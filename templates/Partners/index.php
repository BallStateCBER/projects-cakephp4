<?php
/**
 * @var \App\Model\Entity\Partner[]|\Cake\ORM\ResultSet $partners
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */
?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<table class="partners table">
    <?php foreach ($partners as $partner): ?>
        <tr>
            <td>
                <?= $partner->name ?>
            </td>
            <td class="actions">
                <?= $this->Html->link(
                    'Edit',
                    [
                        'action' => 'edit',
                        $partner->id,
                    ]
                ) ?>
                <?php
                    $prompt = 'Are you sure you want to delete this?';
                    if ($partner->releases) {
                        $count = count($partner->releases);
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
                            $partner->id,
                        ],
                        ['confirm' => $prompt]
                    );
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
