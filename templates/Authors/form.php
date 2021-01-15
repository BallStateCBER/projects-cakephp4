<?php
/**
 * @var \App\Model\Entity\Author $author
 * @var \App\View\AppView $this
 */
?>

<p>
    <?= $this->Html->link(
        'List Authors',
        ['action' => 'index'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<div class="authors form">
    <?= $this->Form->create($author) ?>
    <?= $this->Form->control('name') ?>
    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
