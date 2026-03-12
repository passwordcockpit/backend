<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
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
     * @param int $id
     * @param array $filter
     */
    public function delete($id, $filter);

    /**
     *
     * @param int $id
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
     * @param int $id
     * @param array $data
     */
    public function update($id, $data);
}
