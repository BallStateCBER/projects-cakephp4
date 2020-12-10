<?php
/**
 * @var \App\Model\Entity\User[]|\Cake\ORM\ResultSet $users
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */

?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<p>
    <?= $this->Html->link(
        'Add a New User',
        ['action' => 'add'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<div class="users_index">
    <table class="table">
        <thead>
            <tr>
                <th>
                    Name
                </th>
                <th>
                    Email
                </th>
                <th class="actions">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= $user->name ?>
                    </td>
                    <td>
                        <a href="mailto:<?= $user->email ?>">
                            <?= $user->email ?>
                        </a>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(
                            'Edit',
                            [
                                'action' => 'edit',
                                $user->id,
                            ]
                        ) ?>
                        <?= $this->Form->postLink(
                            'Delete',
                            [
                                'action' => 'delete',
                                $user->id,
                            ],
                            [
                                'confirm' => sprintf(
                                    'Are you sure you want to delete %s\'s account?',
                                    $user->name
                                ),
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
