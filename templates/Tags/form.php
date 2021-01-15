<?php
/**
 * @var \App\Model\Entity\Tag $tag
 * @var \App\View\AppView $this
 */
?>

<p>
    <?= $this->Html->link(
        'List Tags',
        ['action' => 'index'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<div class="tags form">
    <?= $this->Form->create($tag) ?>
    <?= $this->Form->control('name') ?>
    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
