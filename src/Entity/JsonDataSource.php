<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class JsonDataSource extends AbstractDataSource
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $root;

    public function setRoot(?string $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function getRoot(): ?string
    {
        return $this->root;
    }
}
