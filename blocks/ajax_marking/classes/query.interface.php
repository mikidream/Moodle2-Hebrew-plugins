<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    block
 * @subpackage ajax_marking
 * @copyright  2012 Matt Gibson
 * @author     Matt Gibson {@link http://moodle.org/user/view.php?id=81450}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines methods needed by the query objects and decorators
 */
interface block_ajax_marking_query {

    /**
     * Adds a new select clause, which may include aggregate functions etc.
     *
     * @abstract
     * @param array $column
     * @param bool $prefix
     * @param bool $replace
     * @return mixed
     */
    public function add_select(array $column, $prefix = false, $replace = false);

    /**
     * This will add a join table. No alias means it'll use the table name.
     *
     * @param array $table containing 'join', 'table', 'alias', 'on', 'subquery' (last one optional)
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @return void
     */
    public function add_from(array $table);

    /**
     * Adds a condition to the WHERE part of the query.
     *
     * @abstract
     * @param array $clause
     * @return mixed
     */
    public function add_where(array $clause);

    /**
     * Adds a clause to the ORDER BY part of the query after joining together all the bits
     * of the orderby array.
     *
     * @abstract
     * @param $column
     * @param bool $prefix
     * @return mixed
     */
    public function add_orderby($column, $prefix = false);

    /**
     * Adds an array of params.
     *
     * @abstract
     * @param array $params
     * @param bool $arraytoaddto
     * @return mixed
     */
    public function add_params(array $params, $arraytoaddto = false);

    /**
     * Adds one item to the params array. Always use SQL_PARAMS_NAMED.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function add_param($name, $value);

    /**
     * Runs the query using standard Moodle DB functions and returns the result.
     *
     * @abstract
     * @param bool $returnrecordset
     * @return mixed
     */
    public function execute($returnrecordset = false);
}
