<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Nelmio\Alice\Definition\Object;

use Nelmio\Alice\ObjectInterface;

class ImmutableObject implements ObjectInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var object
     */
    private $instance;

    /**
     * @param string $id
     * @param object $instance
     */
    public function __construct(string $id, $instance)
    {
        $this->id = $id;
        $this->instance = deep_clone($instance);
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getInstance()
    {
        return deep_clone($this->instance);
    }
}
