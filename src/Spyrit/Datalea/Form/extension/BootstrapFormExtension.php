<?php

namespace Spyrit\Datalea\Form\extension;

use Symfony\Component\Form\AbstractExtension;

/**
 * BootstrapFormExtension
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class BootstrapFormExtension extends AbstractExtension
{
    protected function loadTypeExtensions()
    {
        return array(
            new Type\RowContainerFormTypeExtension(),
            new Type\HelpFormTypeExtension(),
        );
    }
}