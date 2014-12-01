<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\I18n\Time;
use Cake\Validation\Validator;


class TodosTable extends Table {

	/**
	 * initialize method
	 *
	 * @param  array  $config list of config options
	 * @return none
	 */
	public function initialize(array $config) {
		$this->addBehavior('Timestamp' , [
			'events' => [
				'Model.beforeSave' => [
				'created' => 'new',
				'updated' => 'always'
			]
		]]);
	}

	/**
	 * Default validator method
	 *
	 * @param  Validator $validator
	 * @return Validator $validator
	 */
	public function validationDefault(Validator $validator) {
		$validator
		->allowEmpty('todo', 'update')
		->notEmpty('todo');

		return $validator;
	}

	/**
	 * Custom finder method, returns recent to-do's based on status
	 *
	 * @param  Query  $query   query object
	 * @param  array  $options list of options
	 * @return query  $query
	 */
	public function findRecent(Query $query, array $options) {
		if (empty($options)) {
			$options['status'] = 0;
		}
		$query = $this->find()
				->where(['is_done' => $options['status']])
				->order(['updated' => 'DESC'])
				->map(function ($row) {
					$timeCreated = new Time($row->created);
					$timeUpdated = new Time($row->updated);

					$row->created = $timeCreated->timeAgoInWords();
					$row->updated = $timeUpdated->timeAgoInWords();
				return $row;
			});
		//debug($query);
		return $query;
	}
}
