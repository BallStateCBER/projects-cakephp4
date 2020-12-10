<?php
/**
 * @var \App\Model\Entity\User $user
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */
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
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend>Add User</legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('email');
            echo $this->Form->control('password');
        ?>
    </fieldset>
    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
