<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Abstracts;

interface FacadeInterface
{
    /**
     *
     * @param array $data
     */
    public function create($data);

    /**
     *
     * @param type $id
     * @param type $filter
     */
    public function delete($id, $filter);

    /**
     *
     * @param string $id
     * @param array $filter
     */
    public function fetch($id, $filter);

    /**
     *
     * @param array $filter
     */
    public function fetchAll($filter);

    /**
     *
     * @param string $id
     * @param array $data
     */
    public function update($id, $data);
}
