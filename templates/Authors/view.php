<?php
/**
 * @var \App\Model\Entity\Author $author
 * @var \App\View\AppView $this
 */
?>

<?php if ($author->releases): ?>
    <p>
        The following releases were created or contributed to by <?= $author->name ?>:
    </p>
    <?= $this->element('release_list', ['releases' => $author->releases]) ?>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
