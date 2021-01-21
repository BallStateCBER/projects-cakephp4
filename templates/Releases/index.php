<?php
/**
 * @var \App\Model\Entity\Release[]|\Cake\Collection\CollectionInterface $releases
 * @var \App\View\AppView $this
 */
?>

<div class="releases">
	<?php foreach ($releases as $release): ?>
		<?= $this->element('release', compact('release')) ?>
	<?php endforeach; ?>
</div>

<?= $this->element('DataCenter.pagination') ?>
