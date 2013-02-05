<?php

namespace Spyrit\Datalea\Form\extension\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * RowContainerFormTypeExtension
 *
 * @author Charles Sanquer <charles.sanquer@spyrit.net>
 */
class RowContainerFormTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['row_attr'] = $options['row_attr'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'row_attr' => array(),
        ))
        ->setAllowedTypes(array(
            'row_attr' => 'array',
        ));
    }
    public function getExtendedType()
    {
        return 'form';
    }
}