<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Releases Model
 *
 * @property \App\Model\Table\PartnersTable&\Cake\ORM\Association\BelongsTo $Partners
 * @property \App\Model\Table\GraphicsTable&\Cake\ORM\Association\HasMany $Graphics
 * @property \App\Model\Table\AuthorsTable&\Cake\ORM\Association\BelongsToMany $Authors
 * @property \App\Model\Table\TagsTable&\Cake\ORM\Association\BelongsToMany $Tags
 *
 * @method \App\Model\Entity\Release newEmptyEntity()
 * @method \App\Model\Entity\Release newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Release[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Release get($primaryKey, $options = [])
 * @method \App\Model\Entity\Release findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Release patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Release[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Release|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Release saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Release[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Release[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Release[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Release[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReleasesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('releases');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Slug.Slug');

        $this->belongsTo('Partners', [
            'foreignKey' => 'partner_id',
        ]);
        $this->hasMany('Graphics', [
            'foreignKey' => 'release_id',
        ]);
        $this->belongsToMany('Authors', [
            'foreignKey' => 'release_id',
            'targetForeignKey' => 'author_id',
            'joinTable' => 'authors_releases',
        ]);
        $this->belongsToMany('Tags', [
            'foreignKey' => 'release_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'releases_tags',
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
            ->maxLength('title', 200)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 100)
            ->notEmptyString('slug');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        $validator
            ->date('released')
            ->requirePresence('released', 'create')
            ->notEmptyDate('released');

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
        $rules->add($rules->existsIn(['partner_id'], 'Partners'), ['errorField' => 'partner_id']);

        return $rules;
    }
}
