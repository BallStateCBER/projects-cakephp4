<?php
/**
 * @var \App\Model\Entity\Release[]|\Cake\ORM\ResultSet $releases
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */
?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<p>
    <?= $this->Html->link(
        'Add a New Release',
        ['action' => 'add'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<table class="table" id="releases-admin">
    <thead>
        <tr>
            <th>
                Title
            </th>
            <th>
                Published
            </th>
            <th>
                Added to website
            </th>
            <th>
                Actions
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($releases as $release): ?>
            <tr>
                <td>
                    <?= $this->Html->link(
                        $release->title,
                        [
                            'action' => 'view',
                            'id' => $release->id,
                            'slug' => $release->slug,
                        ]
                    ) ?>
                </td>
                <td>
                    <?= $release->released->format('M j, Y') ?>
                </td>
                <td>
                    <?= $release->created->format('M j, Y') ?>
                </td>
                <td class="actions">
                    <?= $this->Html->link(
                        'Edit',
                        [
                            'action' => 'edit',
                            $release->id,
                        ]
                    ) ?>
                    <?php
                    $prompt = 'Are you sure you want to delete this?';
                    echo $this->Form->postLink(
                        'Delete',
                        [
                            'action' => 'delete',
                            $release->id,
                        ],
                        ['confirm' => $prompt]
                    );
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
