<?php

namespace api\Models;

use DateTime;

class Messages
{
    /** @var int $id */
    private $id;

    /** @var int $createdby */
    private $createdby;

    /** @var DateTime $createdat */
    private $createdat;

    /** @var string $body */
    private $body;

    /** @var bool $isinternal */
    private $isinternal;
}
