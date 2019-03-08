<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MoAdmins
 *
 * @ORM\Table(name="mo_admins")
 * @ORM\Entity
 */
class MoAdmins
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
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=100, nullable=false)
     */
    private $login;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=99, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=99, nullable=false)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="level", type="boolean", nullable=false, options={"unsigned"=true,"default"="1"})
     */
    private $level = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="custom_access", type="text", length=65535, nullable=true)
     */
    private $customAccess;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00"})
     */
    private $lastLogin = '0000-00-00 00:00:00';

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_ip", type="string", length=40, nullable=true)
     */
    private $lastIp = '';

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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getLevel(): ?bool
    {
        return $this->level;
    }

    public function setLevel(bool $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getCustomAccess(): ?string
    {
        return $this->customAccess;
    }

    public function setCustomAccess(?string $customAccess): self
    {
        $this->customAccess = $customAccess;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getLastIp(): ?string
    {
        return $this->lastIp;
    }

    public function setLastIp(?string $lastIp): self
    {
        $this->lastIp = $lastIp;

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
