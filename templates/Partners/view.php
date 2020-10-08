<?php
/**
 * @var \App\Model\Entity\Partner $partner
 * @var \App\View\AppView $this
 * @var string $pageTitle
 */
?>
<h1 class="page_title">
    <?= $pageTitle ?>
</h1>

<?php if ($partner->releases): ?>
    <?= $this->element('release_list', ['releases' => $partner->releases]) ?>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
