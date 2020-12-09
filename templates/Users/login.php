<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="users form">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= 'Please enter your email address and password' ?></legend>
        <?= $this->Form->control('email', ['label' => 'Email', 'required' => true]) ?>
        <?= $this->Form->control('password', ['label' => 'Password', 'required' => true]) ?>
        <?= $this->Form->control('remember_me', [
            'type' => 'checkbox',
            'label' => 'Remember me',
            'checked' => true,
        ]) ?>
        <?= $this->Html->link('Reset Password', ['action' => 'requestResetPassword']) ?>
    </fieldset>
    <?= $this->Form->button('Login', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
