<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Upload\GraphicsPathProcessor;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use DirectoryIterator;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Josegonzalez\Upload\Validation\UploadValidation;

/**
 * Graphics Model
 *
 * @property \App\Model\Table\ReleasesTable&\Cake\ORM\Association\BelongsTo $Releases
 * @method \App\Model\Entity\Graphic newEmptyEntity()
 * @method \App\Model\Entity\Graphic newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Graphic[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Graphic get($primaryKey, $options = [])
 * @method \App\Model\Entity\Graphic findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Graphic patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Graphic[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Graphic|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Graphic saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Graphic[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Graphic[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Graphic[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Graphic[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Josegonzalez\Upload\Model\Behavior\UploadBehavior
 */
class GraphicsTable extends Table
{
    const GRAPHICS_DIR_ROOT = ROOT . DS . 'webroot' . DS . 'img' . DS . 'releases' . DS;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('graphics');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'image' => [
                'path' => '', // filesystem.root + getNextDirNumber(), appended by GraphicsPathProcessor
                'pathProcessor' => GraphicsPathProcessor::class,
                'filesystem' => [
                    'root' => self::GRAPHICS_DIR_ROOT,
                ],
                'fields' => ['dir' => 'dir'],
                'keepFilesOnDelete' => false,
                'transformer' => function ($table, $entity, $data, $field, $settings, $filename) {
                    /** @var \App\Model\Entity\Graphic $entity */
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);

                    // Store the thumbnail in a temporary file
                    $tmp = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension;

                    // Use the Imagine library to DO THE THING
                    $size = new Box(195, 195);
                    $mode = ImageInterface::THUMBNAIL_INSET;
                    $imagine = new Imagine();

                    // Save that modified file to our temp file
                    $imagine->open($data->getStream()->getMetadata('uri'))
                        ->thumbnail($size, $mode)
                        ->save($tmp);

                    // Now return the original *and* the thumbnail
                    return [
                        $data->getStream()->getMetadata('uri') => $filename,
                        $tmp => $entity->thumbnail,
                    ];
                },
                'deleteCallback' => function ($path, $entity, $field, $settings) {
                    /** @var \App\Model\Entity\Graphic $entity */
                    /* When deleting the entity, both the original and the thumbnail will be removed when
                     * keepFilesOnDelete is set to false */

                    return [
                        $path . DS . $entity->{$field},
                        $path . DS . $entity->thumbnail,
                    ];
                },
            ],
        ]);

        $this->belongsTo('Releases', [
            'foreignKey' => 'release_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 100)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('url')
            ->maxLength('url', 200)
            ->requirePresence('url', 'create')
            ->notEmptyString('url');

        $validator
            ->requirePresence('image', 'create')
            ->notEmptyFile('image')
            ->setProvider('upload', UploadValidation::class)
            ->add('image', 'fileUnderPhpSizeLimit', [
                'rule' => 'isUnderPhpSizeLimit',
                'message' => 'The uploaded image exceeds the filesize limit',
                'provider' => 'upload',
            ])
            ->add('image', 'fileCompletedUpload', [
                'rule' => 'isCompletedUpload',
                'message' => 'This image could not be uploaded completely',
                'provider' => 'upload',
            ])
            ->add('image', 'fileFileUpload', [
                'rule' => 'isFileUpload',
                'message' => 'No image file was uploaded',
                'provider' => 'upload',
            ])
            ->add('file', 'fileSuccessfulWrite', [
                'rule' => 'isSuccessfulWrite',
                'message' => 'This upload could not be saved to the server',
                'provider' => 'upload',
            ]);

        $validator
            ->scalar('dir')
            ->maxLength('dir', 255)
            ->requirePresence('dir', 'create')
            ->notEmptyString('dir');

        $validator
            ->integer('weight')
            ->notEmptyString('weight');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['release_id'], 'Releases'), ['errorField' => 'release_id']);

        return $rules;
    }

    /**
     * Returns a directory number for the next release's graphics
     *
     * @return int
     */
    public function getNextDirNumber()
    {
        return $this
            ->find()
            ->select(['dir'])
            ->orderDesc('dir')
            ->first()
            ->dir + 1;
    }

    /**
     * Deletes empty directories
     *
     * @param \Cake\Event\EventInterface $event Delete event
     * @param \Cake\Datasource\EntityInterface $entity Graphic entity
     * @param \ArrayObject $options Options array
     * @return void
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        /** @var \App\Model\Entity\Graphic $entity */
        $fullPath = self::GRAPHICS_DIR_ROOT . $entity->dir;

        foreach (new DirectoryIterator($fullPath) as $item) {
            if ($item->isDot()) {
                continue;
            }

            // Do nothing if files are found
            return;
        }

        // Delete directory if no files were found in it
        rmdir($fullPath);
    }
}
