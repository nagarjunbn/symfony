<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefreshTokens
 *
 * @ORM\Table(name="refresh_tokens", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_9BACE7E1C74F2195", columns={"refresh_token"})})
 * @ORM\Entity
 */
class RefreshTokens
{
    /**
     * @var string
     *
     * @ORM\Column(name="refresh_token", type="string", length=128, nullable=false)
     */
    private $refreshToken;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid", type="datetime", nullable=false)
     */
    private $valid;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

