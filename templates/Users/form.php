<?php
/**
 * @var \App\Model\Entity\User $user
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */

$isAddForm = $this->request->getParam('action') == 'add';
?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<p>
    <?= $this->Html->link(
        'List Users',
        ['action' => 'index'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<div class="users form">
    <?php
        echo $this->Form->create($user);
        echo $this->Form->control('name');
        echo $this->Form->control('email');
        echo $this->Form->control(
            $isAddForm ? 'password' : 'new_password',
            [
                'label' => $isAddForm ? 'Password' : 'New Password (leave blank for no change)',
                'type' => 'password',
            ]
        );
        echo $this->Form->submit('Submit', ['class' => 'btn btn-primary']);
        echo $this->Form->end();
    ?>
</div>
