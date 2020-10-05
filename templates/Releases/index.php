<?php
/**
 * @var \App\Model\Entity\Release[] $releases
 * @var \App\View\AppView $this
 */
?>

<?= $this->element('DataCenter.pagination') ?>

<div class="releases">
	<?php foreach ($releases as $release): ?>
		<?= $this->element('release', compact('release')) ?>
	<?php endforeach; ?>
</div>

<?= $this->element('DataCenter.pagination') ?>
