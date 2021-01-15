<?php
/**
 * @var \App\Model\Entity\Partner $partner
 * @var \App\View\AppView $this
 */
?>

<?php if ($partner->releases): ?>
    <?= $this->element('release_list', ['releases' => $partner->releases]) ?>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
