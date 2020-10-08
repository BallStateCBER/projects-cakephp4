<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tag $tag
 */
?>
<h1 class="page_title">
    <?= str_replace(' And ', ' and ', ucwords($tag->name)) ?>
</h1>

<?php if ($tag->releases): ?>
    <?= $this->element('release_list', ['releases' => $tag->releases]) ?>
<?php else: ?>
    <p>
        No associated projects or publications could be found.
    </p>
<?php endif; ?>
