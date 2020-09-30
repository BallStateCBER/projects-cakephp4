<?php
/**
 * @var \App\Model\Entity\Release $release
 */
?>
<div class="release">
    <h1>
        <?= $this->Html->link(
            $release->title,
            [
                'controller' => 'Releases',
                'action' => 'view',
                'id' => $release->id,
                'slug' => $release->slug,
            ]
        ) ?>
    </h1>

    <p class="partner">
        <?= $this->Html->link(
            $release->partner->name,
            [
                'controller' => 'Partners',
                'action' => 'view',
                'id' => $release->partner->id,
                'slug' => $release->partner->slug,
            ]
        ) ?>
    </p>

    <?php if ($this->Auth->user()): ?>
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
                    'escape' => false
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
                ],
                'Are you sure that you want to delete this release?'
            ) ?>
        </span>
    <?php endif; ?>

    <table>
        <tbody>
            <tr>
                <td class="description_col">
                    <?= $release['Release']['description'] ?>

                    <?php if (!empty($release['Tag'])):?>
                        <p class="tags">
                            Tags:
                            <?php
                                $tagLinks = [];
                                foreach ($release->tags as $tag) {
                                    $tagLinks[] = $this->Html->link(
                                        $tag->name,
                                        [
                                            'controller' => 'tags',
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

                    <?php if ($release->authors):?>
                        <p class="authors">
                            <?= __n('Author', 'Authors', count($release->authors)) ?>:
                            <?php
                                $authorLinks = [];
                                foreach ($release->authors as $author) {
                                    $authorLinks[] = $this->Html->link(
                                        $author->name,
                                        [
                                            'controller' => 'Authors',
                                            'action' => 'view',
                                            $author->id,
                                        ]
                                    );
                                }
                                echo implode(', ', $authorLinks);
                            ?>
                        </p>
                    <?php endif; ?>

                </td>
                <td class="graphics_col <?= count($release->graphics) > 1 ? 'graphics_col_double' : 'graphics_col_single' ?>">
                    <p class="date">
                        Published <?= $release->released->format('F j, Y') ?>
                    </p>

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
                                                    '<div class="graphic"><img src="%s" /></div>%s',
                                                    $imgSrc,
                                                    $graphic->title
                                                ),
                                                $graphic->url,
                                                ['escape' => false]
                                            );
                                        ?>
                                    </td>
                                    <?php if ($k % 2 == 1 && count($release['Graphic']) > $k + 1): ?>
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
