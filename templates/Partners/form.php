<?php
/**
 * @var \App\Model\Entity\Partner $partner
 * @var \App\View\AppView $this
 */
?>

<p>
    <?= $this->Html->link(
        'List Clients, Partners, and Sponsors',
        ['action' => 'index'],
        ['class' => 'btn btn-secondary'],
    ) ?>
</p>

<div class="partners form">
    <?= $this->Form->create($partner) ?>
    <?= $this->Form->control('name', ['label' => 'Full Name']) ?>
    <?= $this->Form->control('short_name', ['label' => 'Short Name (abbreviated where possible)']) ?>
    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>
