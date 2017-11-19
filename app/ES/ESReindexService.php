<?php

namespace App\ES;

abstract class ESReindexService
{
    /**
     * Reindexing index type
     */
    abstract public function reindex();
}
