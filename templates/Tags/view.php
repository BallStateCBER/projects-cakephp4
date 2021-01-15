<?php
/**
 * @var \App\Model\Entity\Tag $tag
 * @var \App\View\AppView $this
 */
?>

<?php if ($tag->releases): ?>
    <?= $this->element('release_list', ['releases' => $tag->releases]) ?>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
