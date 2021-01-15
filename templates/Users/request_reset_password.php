<?php
/**
 * @var \App\Model\Entity\User $user
 */
?>

<div class="users form">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend>Please enter your email address to reset your password</legend>
        <?= $this->Form->control('email', ['label' => 'Email address']) ?>
    </fieldset>
    <?= $this->Form->button('Submit', ['class' => 'btn btn-primary']); ?>
    <?= $this->Form->end() ?>
</div>
