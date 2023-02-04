<?php

namespace Pyz\Zed\GraphMasters\Business\Model\Tour;

interface TourReferenceGeneratorInterface
{
    /**
     * @return string
     */
    public function generateReference(): string;

}
