<?php
/**
 * @var \App\Model\Entity\User $user
 * @var string $pageTitle
 */
?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<div class="users form">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend>Enter and confirm your new password</legend>
        <?= $this->Form->control(
            'new_password',
            [
                'type' => 'password',
                'required' => true,
            ]
        ) ?>
        <?= $this->Form->control(
            'password_confirm',
            [
                'type' => 'password',
                'required' => true,
                'label' => 'Confirm password',
            ]
        ) ?>
    </fieldset>
    <?= $this->Form->button('Submit', ['class' => 'btn btn-primary']); ?>
    <?= $this->Form->end() ?>
</div>
