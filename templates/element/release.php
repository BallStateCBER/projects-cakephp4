<?php
/**
 * @var \App\Model\Entity\Release $release
 * @var \App\View\AppView $this
 */

$loggedIn = $this->request->getSession()->read('User');
$graphicsColClass = count($release->graphics ?? []) > 1 ? 'graphics_col_double' : 'graphics_col_single';
$tagLinks = [];
foreach ($release->tags as $tag) {
    $tagLinks[] = $this->Html->link(
        $tag->name,
        [
            'controller' => 'Tags',
            'action' => 'view',
            'id' => $tag->id,
            'slug' => $tag->slug,
        ]
    );
}
$authorLinks = [];
foreach ($release->authors as $author) {
    $authorLinks[] = $this->Html->link(
        $author->name,
        $author->url
    );
}
$headerClass = $this->request->getParam('action') == 'view' ? 'sr-only' : null;
?>
<section class="release">
    <h1 class="<?= $headerClass ?>">
        <?= $this->Html->link(
            $release->title,
            $release->url
        ) ?>
    </h1>

    <div class="under-headline row">
        <div class="partner col-12 col-md-6">
            <?= $this->Html->link(
                $release->partner->name,
                $release->partner->url
            ) ?>
        </div>

        <div class="date col-12 col-md-6 text-md-right">
            Published <?= $release->released->format('F j, Y') ?>
        </div>
    </div>

    <?php if ($loggedIn): ?>
        <span class="controls">
            <?= $this->Html->link(
                'Edit',
                [
                    'controller' => 'Releases',
                    'action' => 'edit',
                    $release->id,
                ],
                [
                    'class' => 'btn btn-secondary',
                    'escape' => false,
                ]
            ) ?>
            <?= $this->Form->postLink(
                'Delete',
                [
                    'controller' => 'Releases',
                    'action' => 'delete',
                    $release->id,
                ],
                [
                    'class' => 'btn btn-danger',
                    'escape' => false,
                    'confirm' => 'Are you sure that you want to delete this release?',
                ],
            ) ?>
        </span>
    <?php endif; ?>

    <div class="description">
        <?= $release->description ?>
    </div>

    <?php if ($release->graphics): ?>
        <section class="graphics">
            <h2>
                Download
            </h2>
            <ul class="list-unstyled">
                <?php foreach ($release->graphics as $k => $graphic): ?>
                    <li>
                        <?= $this->Html->link(
                            sprintf(
                                '<img src="%s" alt="%s" /><br />%s',
                                "/img/releases/$graphic->dir/$graphic->thumbnail",
                                $graphic->title,
                                $graphic->title,
                            ),
                            $graphic->url,
                            ['escape' => false]
                        ) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($release->tags): ?>
        <p class="tags">
            Tags: <?= implode(', ', $tagLinks) ?>
        </p>
    <?php endif; ?>

    <?php if ($release->authors): ?>
        <p class="authors">
            <?= __n('Author', 'Authors', count($release->authors)) ?>:
            <?= implode(', ', $authorLinks) ?>
        </p>
    <?php endif; ?>
</section>
