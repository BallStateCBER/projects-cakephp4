<?php
/**
 * @var \App\Model\Entity\Release $release
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Author[] $authors
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Partner[] $partners
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Tag[] $availableTags
 * @var bool $hasGraphics
 * @var int $graphicsIterator
 * @var int $time
 * @var int $uploadMb
 * @var string $action
 * @var string $pageTitle
 * @var string $token
 * @var string[] $alternateTemplates
 * @var string[] $defaultTemplates
 * @var string[] $graphicUploadTemplate
 * @var string[] $reportFiletypes
 * @var string[] $validExtensions
 */

    use Cake\Utility\Hash;

    $this->Html->script('release_form', ['block' => 'scriptTop']);
    $this->element('DataCenter.font_awesome_init');
    $this->element('DataCenter.rich_text_editor_init', ['selector' => '#description']);

    // Load uploadify library
    $this->Html->script(
        'https://code.jquery.com/jquery-3.5.1.min.js',
        [
            'block' => 'scriptTop',
            'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
            'crossorigin' => 'anonymous',
        ]
    );
    $this->Html->script('/data_center/uploadifive/jquery.uploadifive.min.js', ['block' => 'scriptTop']);
    $this->Html->css('/data_center/uploadifive/uploadifive.css', ['block' => true]);

    $newPartnerOptions = [
        'id' => 'release-new-partner',
        'label' => 'Client, Partner, or Sponsor',
        'type' => 'text',
        'templates' => $alternateTemplates,
    ];
?>

<?php $this->append('buffered'); ?>
    const releaseForm = new ReleaseForm();
    releaseForm.setupUploadify({
        fileSizeLimit: <?= json_encode("{$uploadMb}MB") ?>,
        time: <?= json_encode($time) ?>,
        token: <?= json_encode($token) ?>,
        validExtensions: <?= json_encode(implode('|', $validExtensions)) ?>,
    });
    document.querySelector('body').dataset.graphicsIterator = <?= $graphicsIterator ?>;
<?php $this->end(); ?>

<h1 class="page_title">
    <?= $pageTitle ?>
</h1>
<?php
    echo $this->Form->create(
        $release,
        [
            'id' => 'ReleaseForm',
            'type' => 'file',
        ]
    );
    if ($action == 'edit') {
        echo $this->Form->control('id', ['type' => 'hidden', 'value' => $release->id]);
    }
    echo $this->Form->control('title');
    echo $this->Form->control('released', [
        'type' => 'date',
        'dateFormat' => 'MDY',
        'label' => 'Date Published',
        'minYear' => 1984,
        'maxYear' => date('Y'),
    ]);
?>

<?php if ($partners): ?>
    <div id="choose_partner">
        <?= $this->Form->control('partner_id', [
            'class' => 'partner validate[funcCall[checkPartner]]',
            'empty' => true,
            'id' => 'release-partner-id',
            'label' => 'Client, Partner, or Sponsor',
            'options' => Hash::combine($partners->toArray(), '{n}.id', '{n}.name'),
            'templates' => $alternateTemplates,
            'templateVars' => [
                'after' => ' <button id="add_partner_button" class="btn btn-secondary">Add new</button>'
            ],
        ]) ?>
    </div>
    <div id="add_partner" style="display: none;">
        <?= $this->Form->control(
            'new_partner',
            $newPartnerOptions + [
                'templateVars' => [
                    'after' => ' <button id="choose_partner_button" class="btn btn-secondary">Choose from list</button>'
                ],
            ]
        ) ?>
    </div>
<?php else: ?>
    <?= $this->Form->control('new_partner', $newPartnerOptions) ?>
<?php endif; ?>

<?= $this->Form->control('author', [
    'empty' => true,
    'id' => 'author-select',
    'label' => 'Author(s)',
    'options' => Hash::combine($authors->toArray(), '{n}.id', '{n}.name'),
    'templates' => $alternateTemplates,
    'templateVars' => ['after' => ' <button id="add_author_toggler" class="btn btn-secondary">Add new</button>'],
]) ?>

<div id="new_author" style="display: none;">
    <?= $this->Form->control('new_author', [
        'label' => false,
        'type' => 'text',
        'placeholder' => 'Author\'s name',
        'templates' => $alternateTemplates,
        'templateVars' => [
            'after' => '<button id="add_author_button" class="btn btn-sm btn-secondary">Add</button> ' .
                '<button id="cancel_add_author_button" class="btn btn-sm btn-secondary">Cancel</button>',
        ],
    ]) ?>
</div>

<ul id="authors_container">
    <?php if ($release->authors): ?>
        <?php foreach ($release->authors as $author): ?>
            <li>
                <?= $author['name'] ?>
                <input type="hidden" name="author[]" value="<?= $author->id ?>" />
                <button>
                    X
                </button>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<?= $this->Form->control('description') ?>

<fieldset class="reports">
    <legend>
        Upload Reports
        <a href="#" id="footnote_upload_reports_handle">
            <i class="fas fa-info-circle" title="More info"></i>
        </a>
    </legend>
    <ul class="footnote" style="display: none;" id="footnote_upload_reports">
        <li>
            Click on <strong>Select Files</strong> above to upload one or more documents.
        </li>
        <li>
            Files must have one of the following extensions: <?= $this->Text->toList($reportFiletypes, 'or') ?>.
        </li>
        <?php if ($uploadMb): ?>
            <li>
                Files larger than <?= $uploadMb ?>MB will need to be uploaded via FTP client.
            </li>
        <?php endif; ?>
        <li>
            These files will be uploaded to a reports folder and can be linked to with linked graphics or in a
            release's description.
        </li>
    </ul>
    <div class="input-group">
        <div class="custom-file">
            <input type="file" name="file_upload" id="upload_reports" />
            <label class="custom-file-label" for="upload_reports">Choose file</label>
        </div>
    </div>
    <input type="checkbox" name="overwrite" value="1" id="overwrite_reports" />
    <label for="overwrite_reports">
        Overwrite reports with the same filename
    </label>
</fieldset>

<fieldset class="graphics">
    <legend>
        Linked Graphics
        <a href="#" id="footnote_upload_graphics_handle">
            <i class="fas fa-info-circle" title="More info"></i>
        </a>
    </legend>
    <ul class="footnote" style="display: none;" id="footnote_upload_graphics">
        <li>Images must be .jpg, .jpeg, .gif, or .png.</li>
        <li>Thumbnails (max 195&times;195px) will be automatically generated.</li>
        <li>Graphics with lower order-numbers are displayed first.</li>
    </ul>
    <table class="graphics">
        <thead <?php if (!$hasGraphics): ?>style="display: none;"<?php endif; ?>>
            <th>Remove</th>
            <th>File</th>
            <th>Title</th>
            <th>Link URL</th>
            <th>Order</th>
        </thead>
        <tbody>
            <?php if ($hasGraphics): ?>
                <?php foreach ($release->graphics as $k => $g): ?>
                    <tr>
                        <?php if ($action == 'add'): ?>
                            <td>
                                <button class="remove_graphic">
                                    <i class="fas fa-times-circle" title="Remove"></i>
                                </button>
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="graphics[<?= $k ?>][image]" id="upload-graphic-<?= $k ?>" />
                                        <label class="custom-file-label" for="upload-graphic-<?= $k ?>">
                                            Choose file
                                        </label>
                                    </div>
                                </div>
                            </td>
                        <?php elseif ($action == 'edit'): ?>
                            <td>
                                <?= $this->Form->control(
                                    "graphics.$k.remove",
                                    [
                                        'type' => 'checkbox',
                                        'label' => false,
                                    ]
                                ) ?>
                            </td>
                            <td>
                                <img src="<?= $release->graphics[$k]->thumbnailFullPath ?>" />
                                <?php foreach (['id', 'dir', 'image'] as $field): ?>
                                    <?= $this->Form->control(
                                        "graphics.$k.$field",
                                        [
                                            'value' => $release->graphics[$k]->$field,
                                            'type' => 'hidden',
                                        ]
                                    ) ?>
                                <?php endforeach; ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= $this->Form->control(
                                "graphics.$k.title",
                                [
                                    'label' => false,
                                    'class' => "validate[condRequired[Graphic{$k}Image]]",
                                ]
                            ) ?>
                        </td>
                        <td>
                            <?= $this->Form->control(
                                "graphics.$k.url",
                                [
                                    'label' => false,
                                    'class' => "validate[condRequired[Graphic{$k}Image]]",
                                    'templates' => $alternateTemplates,
                                    'templateVars' => ['after' => sprintf(
                                        '<button title="Find report" class="find_report" id="find_report_button_%d">' .
                                            '<i class="fas fa-search" title="Find report"></i>' .
                                        '</button>',
                                        $k
                                    )],
                                ]
                            ) ?>
                            <?php $this->append('buffered'); ?>
                                document.getElementById(<?= "find_report_button_$k" ?>).addEventListener(
                                    'click',
                                    function(event) {
                                        event.preventDefault();
                                        toggleReportFinder(this, <?= $k ?>);
                                    }
                                );
                            <?php $this->end(); ?>
                        </td>
                        <td>
                            <?= $this->Form->control(
                                "graphics.$k.weight",
                                [
                                    'label' => false,
                                    'type' => 'select',
                                    'options' => range(1, count($release->graphics)),
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="add_graphic">
                <th colspan="4">
                    <a href="#" class="add_graphic">
                        <i class="fas fa-plus-circle"></i> Add a linked graphic
                    </a>
                </th>
            </tr>
            <tr class="dummy-row">
                <td>
                    <a href="#" class="remove_graphic">
                        <i class="fas fa-times-circle" title="Remove"></i>
                    </a>
                </td>
                <td>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" name="graphics[{i}][image]" id="upload-graphic-{i}" required="required" />
                            <label class="custom-file-label" for="upload-graphic-{i}">
                                Choose file
                            </label>
                        </div>
                    </div>
                </td>
                <td>
                    <?= $this->Form->control(
                        'graphics.{i}.title',
                        [
                            'label' => false,
                            'disabled' => true,
                            'required' => true,
                            'class' => 'validate[condRequired[Graphic{i}Image]]',
                        ]
                    ) ?>
                </td>
                <td>
                    <?= $this->Form->control(
                        'graphics.{i}.url',
                        [
                            'label' => false,
                            'disabled' => true,
                            'required' => true,
                            'class' => 'validate[condRequired[Graphic{i}Image]',
                            'templates' => $alternateTemplates,
                            'templateVars' => [
                                'after' => ' <button title="Find report" class="find_report">' .
                                    '<i class="fas fa-search" title="Find report"></i></button>',
                            ],
                        ]
                    ) ?>
                </td>
                <td>
                    <?= $this->Form->control(
                        'graphics.{i}.weight',
                        [
                            'label' => false,
                            'disabled' => true,
                            'type' => 'select',
                            'options' => $release->graphics
                                ? range(1, count($release->graphics) + 1)
                                : [1],
                        ]
                    ) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</fieldset>

<?php
    echo $this->element(
        'DataCenter.Tags/editor',
        [
            'availableTags' => $availableTags->toArray(),
            'selectedTags' => $release->tags ?? [],
            'hideLabel' => true,
            'allowCustom' => true,
        ]
    );
    echo $this->Form->submit('Submit', ['class' => 'btn btn-primary']);
    echo $this->Form->end();
