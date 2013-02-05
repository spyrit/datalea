<?php

namespace Spyrit\Datalea\Form\extension\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * HelpFormTypeExtension
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class HelpFormTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['help_type'] = $options['help_type'];
        $view->vars['help_popover_trigger'] = $options['help_popover_trigger'];
        $view->vars['help_popover_position'] = $options['help_popover_position'];
        $view->vars['help_popover_title'] = $options['help_popover_title'];
        $view->vars['help'] = $options['help'];
        $view->vars['required_asterisk'] = $options['required_asterisk'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'help_type' => 'inline',
            'help_popover_trigger' => 'hover',
            'help_popover_position' => 'top',
            'help_popover_title' => 'Help',
            'help' => null,
            'required_asterisk' => false,
        ))
        ->setAllowedValues(array(
            'help_type' => array('inline', 'block', 'popover'),
            'help_popover_trigger' => array('hover', 'click', ),
            'help_popover_position' => array('top', 'bottom', 'left', 'right',),
        ))
        ->setAllowedTypes(array(
            'required_asterisk' => 'bool',
        ));
    }
    public function getExtendedType()
    {
        return 'form';
    }
}