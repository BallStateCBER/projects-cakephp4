<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Partner $partner
 */
?>
<h1 class="page_title">
    Projects and Publications with <?= $partner->name ?>
</h1>

<?php if ($partner->releases): ?>
    <?= $this->element('release_list', ['releases' => $partner->releases]) ?>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
