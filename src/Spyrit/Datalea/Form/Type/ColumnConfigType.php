<?php

namespace Spyrit\Datalea\Form\Type;

use Spyrit\Datalea\Faker\Model\ColumnConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

/**
 * ColumnConfigType
 *
 * @author Charles SANQUER - Spyrit Systemes <charles.sanquer@spyrit.net>
 */
class ColumnConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $convertMethods = ColumnConfig::getAvailableConvertMethods();
        
        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Name',
                'help' => 'set the column name',
                'help_type' => 'popover',
                'help_popover_position' => 'right',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'control-group span2',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\MinLength(3),
                )
            ))
            ->add('value', 'text', array(
                'required' => true,
                'label' => 'Value',
                'help' => 'set the column value. You can insert some variable pattern <strong>%variable_name%</strong> which will be replaced by random generated value.',
                'help_type' => 'popover',
                'attr' => array(
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'control-group span6',
                ),
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\MinLength(1),
                )
            ))
            ->add('convertMethod', 'choice', array(
                'required' => false,
                'empty_value' => '',
                'label' => 'Convert',
                'help' => 'Select a filter to transform the column value after variable substitution.',
                'help_type' => 'popover',
                'choices' => $convertMethods,
                'attr' => array(
                    'class' => 'select2-empty-box',
                    'style' => 'width: 95%',
                ),
                'row_attr' => array(
                    'class' => 'control-group span3',
                ),
                'constraints' => array(
                    new Constraints\Choice(array(
                        'choices' => array_keys($convertMethods),
                    )),
                )
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Spyrit\Datalea\Faker\Model\ColumnConfig',
        ));
    }
    
    public function getName()
    {
        return 'datalea_generator_column_config';
    }
}