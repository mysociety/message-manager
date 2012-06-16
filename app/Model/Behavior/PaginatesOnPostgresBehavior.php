<?php
/**
 * PaginatesOnPostgres behavior class.
 *
 * overrides the pageinate counter for Postgres 
 * (there seems to be a "GROUP BY" problem in the COUNT queries (i.e., SQL) that Cake
 * autogenerates in the default paginateCount method).
 *
 * If the model's paginated results are throwing errors, try adding PaginatesOnPostgres to the
 * model's $actsAs setting.
 *
 *
 */

class PaginatesOnPostgresBehavior extends ModelBehavior {

	/* custom paginateCount method (for postgres) */
	public function paginateCount(Model $model, $conditions = null, $recursive = 0, $extra = array()) {
		if (!$model->useTable) {
			return 0; // need a table to count in
		} else {
			$dataSource = ConnectionManager::getDataSource($model->useDbConfig); // defaults to 'default'
			$is_psql = $dataSource->config['datasource'] == 'Database/Postgres';
			if ( $dataSource->config['datasource'] == 'Database/Postgres') {
				$pk = $model->primaryKey;
				$sql = "SELECT DISTINCT ON($pk) $pk FROM " . $model->table; // conditions!
				$model->recursive = $recursive;
				$results = $model->query($sql);
				return count($results);
			} else {
				return $model->find('count', array_merge(compact('conditions', 'recursive'), $extra));
			}
		}
	}
	
}
