<?php
/**
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users form">
    <?= $this->Flash->render('auth') ?>
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend>Please enter your email address to reset your password</legend>
        <?= $this->Form->control('reference', ['label' => 'Email address']) ?>
    </fieldset>
    <?= $this->Form->button(__d('cake_d_c/users', 'Submit'), ['class' => 'btn btn-primary']); ?>
    <?= $this->Form->end() ?>
</div>
