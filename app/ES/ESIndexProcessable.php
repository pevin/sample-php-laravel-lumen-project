<?php

namespace App\ES;

interface ESIndexProcessable
{
    /**
     * Process task
     *
     * @param array $task
     * @return mixed
     */
    public function process(array $task);
}
