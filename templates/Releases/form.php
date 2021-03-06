<?php
/**
 * @var \App\Model\Entity\Release $release
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Author[] $authors
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Partner[] $partners
 * @var \Cake\ORM\ResultSet|\App\Model\Entity\Tag[] $availableTags
 * @var int $time
 * @var int $uploadMb
 * @var string $action
 * @var string $pageTitle
 * @var string $token
 * @var string[] $alternateTemplates
 * @var string[] $buttonAppendTemplate
 * @var string[] $defaultTemplates
 * @var string[] $reportFiletypes
 * @var object $entity
 * @var mixed $errorMsgs
 * @var mixed $extension
 * @var mixed $field
 */

use Cake\Utility\Hash;

$this->Html->script('release_form', ['block' => 'scriptTop']);
$this->element('DataCenter.font_awesome_init');
$this->element('DataCenter.rich_text_editor_init', ['selector' => '#description']);

$newPartnerOptions = [
    'id' => 'release-new-partner',
    'label' => 'Client, Partner, or Sponsor',
    'type' => 'text',
    'templates' => $alternateTemplates,
];

$validReportExtensions = array_map(
    function ($extension) {
        return ".$extension";
    },
    $reportFiletypes
);

$validReportWildcardExtensions = array_map(
    function ($extension) {
        return "*.$extension";
    },
    $reportFiletypes
);
?>

<?php $this->append('scriptTop'); ?>
    <script>
        window.FileAPI = {
            debug: true,
            staticPath: '/fileapi/',
            support: {
                html5: true,
                flash: false,
            }
        };
        window.csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;
    </script>
<?php $this->end(); ?>
<?php $this->Html->script('/data_center/fileapi/FileAPI.js', ['block' => 'scriptTop']); ?>
<?php $this->append('buffered'); ?>
    const releaseForm = new ReleaseForm();
    releaseForm.setupUpload({
        fileSizeLimit: <?= json_encode("{$uploadMb}MB") ?>,
        time: <?= json_encode($time) ?>,
        token: <?= json_encode($token) ?>,
        validExtensions: <?= json_encode(implode('|', $validReportWildcardExtensions)) ?>,
    });
<?php $this->end(); ?>

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
    <div id="choose-partner">
        <?= $this->Form->control('partner_id', [
            'empty' => true,
            'id' => 'release-partner-id',
            'label' => 'Client, Partner, or Sponsor',
            'options' => Hash::combine($partners->toArray(), '{n}.id', '{n}.name'),
            'templates' => $alternateTemplates,
            'templateVars' => [
                'after' => ' <button id="add-partner-button" class="btn btn-secondary">Add new</button>',
            ],
        ]) ?>
    </div>
    <div id="add-partner" style="display: none;">
        <?= $this->Form->control(
            'new_partner',
            $newPartnerOptions + [
                'templateVars' => [
                    'after' => ' <button id="choose-partner-button" class="btn btn-secondary">' .
                        'Choose from list</button>',
                ],
            ]
        ) ?>
    </div>
<?php else: ?>
    <?= $this->Form->control('new_partner', $newPartnerOptions) ?>
<?php endif; ?>

<?= $this->Form->control('author_select', [
    'empty' => true,
    'id' => 'author-select',
    'label' => 'Author(s)',
    'options' => Hash::combine($authors->toArray(), '{n}.id', '{n}.name'),
    'templates' => $alternateTemplates,
    'templateVars' => ['after' => ' <button id="add-author-toggler" class="btn btn-secondary">Add new</button>'],
]) ?>

<div id="new-author-container" style="display: none;">
    <?= $this->Form->control('new_author_input', [
        'label' => false,
        'type' => 'text',
        'placeholder' => 'Author\'s name',
        'templates' => $alternateTemplates,
        'templateVars' => [
            'after' => '<button id="add-author-button" class="btn btn-sm btn-secondary">Add</button> ' .
                '<button id="cancel-add-author-button" class="btn btn-sm btn-secondary">Cancel</button>',
        ],
    ]) ?>
</div>

<ul id="authors-container">
    <?php if ($release->authors): ?>
        <?php foreach ($release->authors as $author): ?>
            <li>
                <?= $this->Release->displayErrors($author) ?>
                <?= $author->name ?>
                <input type="hidden" name="authors[_ids][]" value="<?= $author->id ?>" />
                <button>
                    <i class="fas fa-times" title="Remove"></i>
                </button>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<?= $this->Form->control('description', ['required' => false]) ?>

<fieldset class="reports release-form">
    <legend>
        Upload Reports
        <a href="#" id="footnote-upload-reports-handle">
            <i class="fas fa-info-circle" title="More info"></i>
        </a>
    </legend>
    <ul class="footnote" style="display: none;" id="footnote-upload-reports">
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
        <div class="custom-file js-fileapi-wrapper">
            <input type="file" name="file_upload" id="upload-reports"
                   accept="<?= implode(',', $validReportExtensions) ?>" />
            <label class="custom-file-label" for="upload-reports">Choose file</label>
        </div>
    </div>
    <input type="checkbox" name="overwrite" value="1" id="overwrite-reports" />
    <label for="overwrite-reports">
        Overwrite reports with the same filename
    </label>
    <div class="progress" id="upload-reports-progress-container">
        <div class="progress-bar" role="progressbar" style="width: 0" id="upload-reports-progress"></div>
    </div>
    <div id="upload-report-results"></div>
</fieldset>

<fieldset class="graphics release-form">
    <legend>
        Linked Graphics
        <a href="#" id="footnote-upload-graphics-handle">
            <i class="fas fa-info-circle" title="More info"></i>
        </a>
    </legend>
    <ul class="footnote" style="display: none;" id="footnote-upload-graphics">
        <li>Images must be .jpg, .jpeg, .gif, or .png.</li>
        <li>Thumbnails (max 195&times;195px) will be automatically generated.</li>
        <li>Graphics with lower order-numbers are displayed first.</li>
    </ul>
    <table class="graphics">
        <thead <?php if (!$release->graphics): ?>style="display: none;"<?php endif; ?>>
            <th>Remove</th>
            <th>File</th>
            <th>Title</th>
            <th>Link URL</th>
            <th>Order</th>
        </thead>
        <tbody>
            <?php foreach ($release->graphics ?? [] as $k => $g): ?>
                <?php $this->start('uploadGraphicInput'); ?>
                <div class="form-group">
                    <input type="file" name="graphics[<?= $k ?>][image]" accept="image/*"
                           id="upload-graphic-<?= $k ?>" class="form-control-file" />
                    <label class="sr-only" for="upload-graphic-<?= $k ?>">
                        Choose file
                    </label>
                </div>
                <?php $this->end(); ?>
                <?php $errors = $this->Release->displayErrors($g); ?>
                <?php if ($errors): ?>
                    <tr class="errors">
                        <td colspan="5">
                            <?= $errors ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr class="graphic">
                    <?php if ($action == 'add'): ?>
                        <td>
                            <button class="remove-graphic btn btn-outline-danger">
                                <i class="fas fa-times-circle" title="Remove"></i>
                            </button>
                        </td>
                        <td>
                            <?= $this->fetch('uploadGraphicInput') ?>
                        </td>
                    <?php elseif ($action == 'edit'): ?>
                        <td>
                            <?= $this->Form->control(
                                "graphics.$k.remove",
                                [
                                    'type' => 'checkbox',
                                    'label' => false,
                                    'value' => 0,
                                ]
                            ) ?>
                        </td>
                        <td>
                            <?php
                                $imgWebPath = $release->graphics[$k]->thumbnailFullPath;
                                $imgFilePath = WWW_ROOT . str_replace('/', DS, substr($imgWebPath, 1));
                            ?>
                            <?php if (file_exists($imgFilePath)): ?>
                                <img src="<?= $imgWebPath ?>" />
                                <?= $this->Form->control(
                                    "graphics.$k.id",
                                    [
                                        'value' => $release->graphics[$k]->id,
                                        'type' => 'hidden',
                                    ]
                                ) ?>
                            <?php else: ?>
                                <?= $this->fetch('uploadGraphicInput') ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?= $this->Form->control(
                            "graphics.$k.title",
                            ['label' => false],
                        ) ?>
                    </td>
                    <td>
                        <?= $this->Form->control(
                            "graphics.$k.url",
                            [
                                'label' => false,
                                'templates' => $buttonAppendTemplate,
                                'templateVars' => ['after' => sprintf(
                                    '<button title="Find report" id="find-report-button-%d" ' .
                                    'class="btn btn-outline-secondary find-report">' .
                                        '<i class="fas fa-search" title="Find report"></i>' .
                                    '</button>',
                                    $k
                                )],
                            ]
                        ) ?>
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
        </tbody>
        <tfoot>
            <tr class="add-graphic">
                <th colspan="4">
                    <button class="add-graphic btn btn-secondary">
                        <i class="fas fa-plus-circle"></i> Add a linked graphic
                    </button>
                </th>
            </tr>
            <template>
                <tr class="graphic">
                    <td>
                        <button class="remove-graphic btn btn-link">
                            <i class="fas fa-times-circle" title="Remove"></i>
                        </button>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="file" name="graphics[{i}][image]" accept="image/*"
                                   id="upload-graphic-{i}" class="form-control-file" disabled="disabled" />
                            <label class="sr-only" for="upload-graphic-{i}">
                                Choose file
                            </label>
                        </div>
                    </td>
                    <td>
                        <?= $this->Form->control(
                            'graphics.{i}.title',
                            [
                                'label' => false,
                                'disabled' => true,
                                'required' => true,
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
                                'templates' => $buttonAppendTemplate,
                                'templateVars' => [
                                    'after' => '<button title="Find report" ' .
                                        'class="btn btn-outline-secondary find-report">' .
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
            </template>
        </tfoot>
    </table>
</fieldset>

<fieldset class="release-form">
    <legend>Tags</legend>
    <?= $this->element(
        'DataCenter.Tags/editor',
        [
            'availableTags' => $availableTags->toArray(),
            'selectedTags' => $release->tags ?? [],
            'hideLabel' => true,
            'allowCustom' => true,
        ]
    ) ?>
</fieldset>

<?php
    echo $this->Form->submit('Submit', ['class' => 'btn btn-primary']);
    echo $this->Form->end();
