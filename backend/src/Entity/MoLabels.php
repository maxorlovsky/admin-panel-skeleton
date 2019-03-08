<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MoLabels
 *
 * @ORM\Table(name="mo_labels", indexes={@ORM\Index(name="site_id", columns={"site_id"}), @ORM\Index(name="name", columns={"name"})})
 * @ORM\Entity
 */
class MoLabels
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="site_id", type="integer", nullable=false, options={"unsigned"=true,"default"="0"})
     */
    private $siteId = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="output", type="text", length=65535, nullable=true)
     */
    private $output;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false, options={"unsigned"=true,"default"="0"})
     */
    private $deleted = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteId(): ?int
    {
        return $this->siteId;
    }

    public function setSiteId(int $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }


}
