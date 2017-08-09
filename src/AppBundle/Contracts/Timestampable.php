<?php

namespace AppBundle\Contracts;

/**
 * Interface Timestampable
 */
interface Timestampable
{

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $datetime
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $datetime);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $datetime
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $datetime);

}