<?php
/**
 * @var \App\Model\Entity\Release $release
 * @var \App\View\AppView $this
 */

$loggedIn = $this->request->getSession()->read('User');
$graphicsColClass = count($release->graphics ?? []) > 1 ? 'graphics_col_double' : 'graphics_col_single';
?>
<div class="release">
    <?php if ($this->request->getParam('action') !== 'view'): ?>
        <h1>
            <?= $this->Html->link(
                $release->title,
                $release->url
            ) ?>
        </h1>
    <?php endif; ?>

    <p class="partner">
        <?= $this->Html->link(
            $release->partner->name,
            $release->partner->url
        ) ?>
    </p>

    <p class="date">
        Published <?= $release->released->format('F j, Y') ?>
    </p>

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

    <table>
        <tbody>
            <tr>
                <td class="description_col">
                    <?= $release->description ?>

                    <?php if ($release->tags): ?>
                        <p class="tags">
                            Tags:
                            <?php
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
                                echo implode(', ', $tagLinks);
                            ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($release->authors): ?>
                        <p class="authors">
                            <?= __n('Author', 'Authors', count($release->authors)) ?>:
                            <?php
                                $authorLinks = [];
                                foreach ($release->authors as $author) {
                                    $authorLinks[] = $this->Html->link(
                                        $author->name,
                                        $author->url
                                    );
                                }
                                echo implode(', ', $authorLinks);
                            ?>
                        </p>
                    <?php endif; ?>

                </td>
                <td class="graphics_col <?= $graphicsColClass ?>">
                    <?php if ($release->graphics): ?>
                        <table>
                            <tr>
                                <?php foreach ($release->graphics as $k => $graphic): ?>
                                    <?php if ($k + 1 == count($release->graphics) && $k % 2 == 0): ?>
                                        <td>
                                            &nbsp;
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php
                                            $imgSrc = sprintf(
                                                '/img/releases/%s/%s',
                                                $graphic->dir,
                                                $graphic->thumbnail
                                            );
                                            echo $this->Html->link(
                                                sprintf(
                                                    '<div class="graphic"><img src="%s" alt="%s" /></div>%s',
                                                    $imgSrc,
                                                    $graphic->title,
                                                    $graphic->title,
                                                ),
                                                $graphic->url,
                                                ['escape' => false]
                                            );
                                        ?>
                                    </td>
                                    <?php if ($k % 2 == 1 && count($release->graphics) > $k + 1): ?>
                                        </tr>
                                        <tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        </table>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
